<?php

namespace App\Services;

use App\Helpers\LicenseHelper;
use App\Helpers\SettingHelper;

/**
 * Augments the public player count with a stable, license-gated fake offset.
 *
 * The real player count stored on the server is never modified. Only the
 * value returned to templates/APIs/widgets is augmented, and only when the
 * feature is enabled and the license is valid.
 */
class FakePlayerService
{
    public const ENABLED_KEY = 'fake_players_enabled';

    public const INTERVAL_KEY = 'fake_players_interval';

    public const RULES_KEY = 'fake_player_rules';

    /** Default window length in minutes when none is configured. */
    public const DEFAULT_INTERVAL = 10;

    /**
     * Return the real count augmented with a stable fake offset.
     *
     * Falls back to the untouched real count when the feature is disabled,
     * the license is invalid, or no rule matches the current real count.
     */
    public function augment(int $realCount): int
    {
        if (! $this->isEnabled()) {
            return $realCount;
        }

        $rule = $this->matchingRule($realCount);

        if ($rule === null) {
            return $realCount;
        }

        $offset = $this->offsetForRule($rule);

        return $this->applyCap($realCount + $offset);
    }

    /**
     * Whether the fake player overlay is active right now.
     */
    public function isEnabled(): bool
    {
        return (bool) SettingHelper::get(self::ENABLED_KEY, false)
            && LicenseHelper::isValid();
    }

    /**
     * Find the first rule whose real range contains the given count.
     *
     * Rules are evaluated top-to-bottom, so the first match wins.
     *
     * @return array{real_min:int,real_max:int,fake_min:int,fake_max:int}|null
     */
    public function matchingRule(int $realCount): ?array
    {
        foreach ($this->rules() as $index => $rule) {
            $normalized = $this->normalizeRule($rule);

            if ($normalized === null) {
                continue;
            }

            if ($realCount >= $normalized['real_min'] && $realCount <= $normalized['real_max']) {
                // Preserve original index so the offset seed is stable per rule.
                $normalized['index'] = $index;

                return $normalized;
            }
        }

        return null;
    }

    /**
     * Compute a deterministic offset for a rule within the current time window.
     *
     * The value is stable for every request inside the same window and changes
     * to a new value within the fake range once the window rolls over. It is
     * derived purely from the app key, window index and rule index, so it
     * survives cache flushes and never touches the global RNG state.
     *
     * @param  array{real_min:int,real_max:int,fake_min:int,fake_max:int,index?:int}  $rule
     */
    protected function offsetForRule(array $rule): int
    {
        $span = $rule['fake_max'] - $rule['fake_min'] + 1;

        if ($span <= 1) {
            return $rule['fake_min'];
        }

        $seed = sprintf(
            '%s|%d|%d',
            (string) config('app.key'),
            $this->currentWindowIndex(),
            $rule['index'] ?? 0,
        );

        // crc32 is 0..2^32-1 on 64-bit PHP; mask to a non-negative int.
        $hash = crc32($seed) & 0x7FFFFFFF;

        return $rule['fake_min'] + ($hash % $span);
    }

    /**
     * The index of the current time window (changes every `interval` minutes).
     */
    protected function currentWindowIndex(): int
    {
        $intervalSeconds = max(1, $this->intervalMinutes() * 60);

        return intdiv(now()->getTimestamp(), $intervalSeconds);
    }

    /**
     * Configured window length in minutes (defaults to DEFAULT_INTERVAL).
     */
    public function intervalMinutes(): int
    {
        $interval = (int) SettingHelper::get(self::INTERVAL_KEY, self::DEFAULT_INTERVAL);

        return $interval > 0 ? $interval : self::DEFAULT_INTERVAL;
    }

    /**
     * Cap the augmented count at the configured max player capacity, if any,
     * so the public count never exceeds the advertised maximum.
     */
    protected function applyCap(int $count): int
    {
        $max = (int) SettingHelper::get('sro_max_player', 0);

        if ($max > 0 && $count > $max) {
            return $max;
        }

        return $count;
    }

    /**
     * Raw configured rules.
     *
     * @return array<int, mixed>
     */
    protected function rules(): array
    {
        $rules = SettingHelper::get(self::RULES_KEY, []);

        return is_array($rules) ? array_values($rules) : [];
    }

    /**
     * Validate and coerce a single rule row into integers.
     *
     * @param  mixed  $rule
     * @return array{real_min:int,real_max:int,fake_min:int,fake_max:int}|null
     */
    protected function normalizeRule($rule): ?array
    {
        if (! is_array($rule)) {
            return null;
        }

        foreach (['real_min', 'real_max', 'fake_min', 'fake_max'] as $field) {
            if (! isset($rule[$field]) || ! is_numeric($rule[$field])) {
                return null;
            }
        }

        $normalized = [
            'real_min' => (int) $rule['real_min'],
            'real_max' => (int) $rule['real_max'],
            'fake_min' => (int) $rule['fake_min'],
            'fake_max' => (int) $rule['fake_max'],
        ];

        if ($normalized['real_min'] > $normalized['real_max']
            || $normalized['fake_min'] > $normalized['fake_max']) {
            return null;
        }

        return $normalized;
    }
}

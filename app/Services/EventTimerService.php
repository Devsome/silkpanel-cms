<?php

namespace App\Services;

use App\Models\EventTimer;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class EventTimerService
{
    private const CACHE_KEY = 'event_timers';
    private const CACHE_TTL = 3600;

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public static function getTimers(): Collection
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return EventTimer::orderBy('sort_order')->get();
        });
    }

    /**
     * Get all timers with their computed next event time.
     */
    public static function getTimersWithCountdown(?Carbon $now = null): array
    {
        $now = $now ?? Carbon::now();

        return self::getTimers()->map(function (EventTimer $timer) use ($now) {
            return [
                'name' => $timer->name,
                'type' => $timer->type,
                'icon' => $timer->icon,
                'image' => $timer->image,
                'time' => $timer->time,
                'next_event' => $timer->isStatic() ? null : self::calculateNextEvent($timer, $now),
            ];
        })->toArray();
    }

    /**
     * Calculate the next occurrence of a dynamic timer.
     */
    public static function calculateNextEvent(EventTimer $timer, ?Carbon $now = null): ?Carbon
    {
        $now = $now ?? Carbon::now();

        if ($timer->isStatic()) {
            return null;
        }

        // Recurring hourly: runs at specific hours each day
        if (is_array($timer->hours) && count($timer->hours) > 0) {
            return self::calculateNextFromHours($timer->hours, $timer->min, $now);
        }

        // Weekly: runs on specific days at a fixed hour:min
        if (is_array($timer->days) && count($timer->days) > 0 && $timer->hour !== null) {
            return self::calculateNextFromDays($timer->days, $timer->hour, $timer->min, $now);
        }

        return null;
    }

    private static function calculateNextFromHours(array $hours, int $min, Carbon $now): Carbon
    {
        $candidates = [];

        // Check today and tomorrow
        for ($dayOffset = 0; $dayOffset <= 1; $dayOffset++) {
            $day = $now->copy()->addDays($dayOffset);
            foreach ($hours as $hour) {
                $candidate = $day->copy()->setTime((int) $hour, $min, 0);
                if ($candidate->greaterThan($now)) {
                    $candidates[] = $candidate;
                }
            }
        }

        if (empty($candidates)) {
            // Fallback: first hour of the day after tomorrow
            sort($hours);
            return $now->copy()->addDays(2)->setTime((int) $hours[0], $min, 0);
        }

        usort($candidates, fn(Carbon $a, Carbon $b) => $a->timestamp <=> $b->timestamp);

        return $candidates[0];
    }

    private static function calculateNextFromDays(array $days, int $hour, int $min, Carbon $now): Carbon
    {
        $candidates = [];

        // Check the next 7 days
        for ($dayOffset = 0; $dayOffset <= 7; $dayOffset++) {
            $day = $now->copy()->addDays($dayOffset);
            $dayName = $day->format('l'); // e.g. "Sunday"

            if (in_array($dayName, $days, true)) {
                $candidate = $day->copy()->setTime($hour, $min, 0);
                if ($candidate->greaterThan($now)) {
                    $candidates[] = $candidate;
                }
            }
        }

        if (empty($candidates)) {
            // Shouldn't happen with 8-day window, but fallback
            return $now->copy()->addWeek()->setTime($hour, $min, 0);
        }

        usort($candidates, fn(Carbon $a, Carbon $b) => $a->timestamp <=> $b->timestamp);

        return $candidates[0];
    }
}

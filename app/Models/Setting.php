<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property string $key
 * @property mixed $value
 * @property string|null $type
 * @property string|null $label
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Setting extends Model
{
    private const CACHE_KEY_PREFIX = 'setting.';

    private const CACHE_ALL_KEY = 'settings.all';

    protected $table = 'settings';

    protected $fillable = [
        'key',
        'value',
        'type',
        'label',
        'description',
    ];

    protected $casts = [
        'value' => 'json',
    ];

    /**
     * Get a setting by key
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $cacheKey = self::CACHE_KEY_PREFIX . $key;

        if (self::supportsTaggedCache()) {
            return Cache::tags(['settings', "key:{$key}"])->rememberForever($cacheKey, function () use ($key, $default) {
                $setting = static::query()->where('key', $key)->first();

                return $setting?->value ?? $default;
            });
        }

        return Cache::rememberForever($cacheKey, function () use ($key, $default) {
            $setting = static::query()->where('key', $key)->first();

            return $setting?->value ?? $default;
        });
    }

    /**
     * Set a setting by key
     */
    public static function set(string $key, mixed $value, ?string $type = null, ?string $label = null, ?string $description = null): static
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'label' => $label,
                'description' => $description,
            ]
        );

        self::forgetCache($key);

        return $setting;
    }

    /**
     * Get all settings grouped by type or as array
     */
    public static function getAllSettings(): array
    {
        if (self::supportsTaggedCache()) {
            return Cache::tags(['settings'])->rememberForever(self::CACHE_ALL_KEY, function () {
                return static::query()
                    ->get()
                    ->mapWithKeys(fn($setting) => [$setting->key => $setting->value])
                    ->toArray();
            });
        }

        return Cache::rememberForever(self::CACHE_ALL_KEY, function () {
            return static::query()
                ->get()
                ->mapWithKeys(fn($setting) => [$setting->key => $setting->value])
                ->toArray();
        });
    }

    /**
     * Delete a setting by key
     *
     * @param string $key
     * @return boolean
     */
    public static function deleteByKey(string $key): bool
    {
        $deleted = static::query()->where('key', $key)->delete() > 0;

        if ($deleted) {
            self::forgetCache($key);
        }

        return $deleted;
    }

    /**
     * Forget cache for a specific key or all settings
     *
     * @param string|null $key
     * @return void
     */
    public static function forgetCache(?string $key = null): void
    {
        if ($key !== null) {
            $cacheKey = self::CACHE_KEY_PREFIX . $key;

            if (self::supportsTaggedCache()) {
                Cache::tags(['settings', "key:{$key}"])->forget($cacheKey);
            } else {
                Cache::forget($cacheKey);
            }
        }

        if (self::supportsTaggedCache()) {
            Cache::tags(['settings'])->forget(self::CACHE_ALL_KEY);

            return;
        }

        Cache::forget(self::CACHE_ALL_KEY);
    }

    /**
     * Check if the cache store supports tagging
     *
     * @return boolean
     */
    private static function supportsTaggedCache(): bool
    {
        return method_exists(Cache::getStore(), 'tags');
    }
}

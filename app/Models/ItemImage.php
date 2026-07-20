<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ItemImage extends Model
{
    protected $fillable = [
        'codename',
        'image',
    ];

    private const CACHE_KEY = 'item_images.map';

    private const CACHE_BASENAME_KEY = 'item_images.basename_map';

    /**
     * Full codename => storage image path.
     */
    public static function getImageMap(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return static::query()
                ->pluck('image', 'codename')
                ->toArray();
        });
    }

    /**
     * Basename-only codename => storage image path.
     */
    public static function getBasenameMap(): array
    {
        return Cache::rememberForever(self::CACHE_BASENAME_KEY, function () {
            return static::query()
                ->get(['codename', 'image'])
                ->mapWithKeys(fn ($item) => [
                    basename($item->codename) => $item->image,
                ])
                ->toArray();
        });
    }

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget(self::CACHE_BASENAME_KEY);
    }

    protected static function booted(): void
    {
        static::saved(fn () => self::clearCache());
        static::deleted(fn () => self::clearCache());
    }
}

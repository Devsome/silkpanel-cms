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

    /**
     * Codename => storage image path.
     */
    public static function getImageMap(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return static::query()
                ->pluck('image', 'codename')
                ->toArray();
        });
    }

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    protected static function booted(): void
    {
        static::saved(fn () => self::clearCache());
        static::deleted(fn () => self::clearCache());
    }
}

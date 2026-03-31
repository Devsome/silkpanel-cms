<?php

namespace App\Models;

use App\Helpers\SettingHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Page extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'slug',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];


    protected $appends = ['languages_status'];

    public function translations()
    {
        return $this->hasMany(PageTranslation::class);
    }

    public function translation($locale = null)
    {
        $locale ??= app()->getLocale();

        return $this->hasOne(PageTranslation::class)
            ->where('locale', $locale);
    }

    protected static function booted(): void
    {
        static::creating(function ($page) {
            $page->slug = static::generateUniqueSlug($page->slug);
        });

        static::updating(function ($page) {
            if ($page->isDirty('slug')) {
                $page->slug = static::generateUniqueSlug($page->slug, $page->id);
            }
        });
    }

    protected static function generateUniqueSlug(?string $slug, ?int $ignoreId = null): string
    {
        $slug = Str::slug($slug ?: 'page');

        $originalSlug = $slug;
        $counter = 1;

        while (
            static::where('slug', $slug)
            ->when($ignoreId, fn($query) => $query->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $originalSlug . '-' . $counter++;
        }

        return $slug;
    }

    public function getLanguagesStatusAttribute()
    {
        $frontendLanguages = SettingHelper::frontendLanguagesWithLabels();

        return collect($frontendLanguages)->map(function ($label, $locale) {
            $t = $this->translations->firstWhere('locale', $locale);

            return $t && $t->is_complete
                ? __('filament/pages.translated') . " ({$locale})"
                : __('filament/pages.not_translated') . " ({$locale})";
        })
            ->implode(',')
        ;
    }
}

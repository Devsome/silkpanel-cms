<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageTranslation extends Model
{
    protected $fillable = [
        'page_id',
        'locale',
        'title',
        'content',
        'seo_title',
        'seo_description',
        'is_complete',
    ];

    protected $casts = [
        'content' => 'array',
        'is_complete' => 'boolean',
    ];

    protected $attributes = [
        'content' => '[]',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}

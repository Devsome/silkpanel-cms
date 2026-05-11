<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $order
 * @property bool $enabled
 * @property \Illuminate\Support\Carbon|null $available_from
 * @property \Illuminate\Support\Carbon|null $available_until
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class WebmallCategory extends Model
{
    protected $table = 'webmall_categories';

    protected $fillable = [
        'name',
        'slug',
        'order',
        'enabled',
        'available_from',
        'available_until',
        'schedule_days',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'order' => 'integer',
        'available_from' => 'datetime',
        'available_until' => 'datetime',
        'schedule_days' => 'array',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(WebmallCategoryItem::class);
    }

    public function activeItems(): HasMany
    {
        return $this->hasMany(WebmallCategoryItem::class)
            ->where('enabled', true)
            ->where(function ($q) {
                $q->whereNull('available_from')->orWhere('available_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('available_until')->orWhere('available_until', '>=', now());
            })
            ->where(function ($q) {
                $q->whereNull('stock')->orWhereRaw('stock > sold');
            })
            ->orderBy('order');
    }

    public function purchases(): HasManyThrough
    {
        return $this->hasManyThrough(
            WebmallPurchase::class,
            WebmallCategoryItem::class,
            'webmall_category_id',
            'webmall_category_item_id',
        );
    }
}

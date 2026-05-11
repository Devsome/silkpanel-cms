<?php

namespace App\Models;

use App\Enums\SilkTypeEnum;
use App\Enums\SilkTypeIsroEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use SilkPanel\SilkroadModels\Models\Shard\RefObjCommon;

/**
 * @property int $id
 * @property int $webmall_category_id
 * @property int $ref_item_id
 * @property string|null $item_name_snapshot
 * @property string $price_type
 * @property int $price_value
 * @property bool $is_hot
 * @property \Illuminate\Support\Carbon|null $available_from
 * @property \Illuminate\Support\Carbon|null $available_until
 * @property int|null $stock
 * @property int $sold
 * @property int $order
 * @property bool $enabled
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class WebmallCategoryItem extends Model
{
    protected $table = 'webmall_category_items';

    protected $fillable = [
        'webmall_category_id',
        'ref_item_id',
        'item_name_snapshot',
        'price_type',
        'price_value',
        'is_hot',
        'available_from',
        'available_until',
        'stock',
        'sold',
        'order',
        'enabled',
    ];

    protected $casts = [
        'is_hot' => 'boolean',
        'available_from' => 'datetime',
        'available_until' => 'datetime',
        'enabled' => 'boolean',
        'stock' => 'integer',
        'sold' => 'integer',
        'price_value' => 'integer',
        'order' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(WebmallCategory::class, 'webmall_category_id');
    }

    public function refObj(): BelongsTo
    {
        return $this->belongsTo(RefObjCommon::class, 'ref_item_id', 'ID');
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(WebmallPurchase::class);
    }

    public function isAvailable(): bool
    {
        if (!$this->enabled) {
            return false;
        }
        $now = now();
        if ($this->available_from && $this->available_from > $now) {
            return false;
        }
        if ($this->available_until && $this->available_until < $now) {
            return false;
        }
        if ($this->stock !== null && $this->sold >= $this->stock) {
            return false;
        }
        return true;
    }

    public function remainingStock(): ?int
    {
        if ($this->stock === null) {
            return null;
        }
        return max(0, $this->stock - $this->sold);
    }

    public function isPriceGold(): bool
    {
        return $this->price_type === 'gold';
    }

    public function priceTypeLabel(): string
    {
        return match (true) {
            $this->price_type === 'gold'                                           => 'Gold',
            SilkTypeIsroEnum::tryFrom((int) $this->price_type) !== null            => SilkTypeIsroEnum::from((int) $this->price_type)->getLabel(),
            SilkTypeEnum::tryFrom((string) $this->price_type) !== null             => SilkTypeEnum::from((string) $this->price_type)->getLabel(),
            default                                                                => $this->price_type,
        };
    }
}

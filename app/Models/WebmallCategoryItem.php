<?php

namespace App\Models;

use App\Enums\WebmallItemTypeEnum;
use App\Enums\SilkTypeEnum;
use App\Enums\SilkTypeIsroEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use SilkPanel\SilkroadModels\Models\Shard\RefObjCommon;

/**
 * @property int $id
 * @property int $webmall_category_id
 * @property int $ref_item_id
 * @property string|null $item_name_snapshot
 * @property string|null $custom_image_path
 * @property string|null $custom_database_connection
 * @property string|null $custom_procedure_name
 * @property array<string, mixed>|null $custom_parameters
 * @property string $item_type
 * @property int|null $procedure_mapping_id
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
    use SoftDeletes;
    protected $table = 'webmall_category_items';

    protected static function booted(): void
    {
        static::creating(function (self $item): void {
            $item->normalizeRefItemIdForType();
        });

        static::updating(function (self $item): void {
            $item->normalizeRefItemIdForType();
        });
    }

    protected $fillable = [
        'webmall_category_id',
        'ref_item_id',
        'item_name_snapshot',
        'custom_image_path',
        'custom_database_connection',
        'custom_procedure_name',
        'custom_parameters',
        'item_type',
        'procedure_mapping_id',
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
        'item_type' => WebmallItemTypeEnum::class,
        'procedure_mapping_id' => 'integer',
        'custom_parameters' => 'array',
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

    public function procedureMapping(): BelongsTo
    {
        return $this->belongsTo(ProcedureMapping::class, 'procedure_mapping_id');
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

    public function isCustomItem(): bool
    {
        return $this->item_type instanceof WebmallItemTypeEnum
            ? $this->item_type === WebmallItemTypeEnum::CUSTOM_ITEM
            : (string) $this->item_type === WebmallItemTypeEnum::CUSTOM_ITEM->value;
    }

    public function isRegularItem(): bool
    {
        return $this->item_type instanceof WebmallItemTypeEnum
            ? $this->item_type === WebmallItemTypeEnum::REGULAR_ITEM
            : (string) $this->item_type === WebmallItemTypeEnum::REGULAR_ITEM->value;
    }

    public function itemTypeLabel(): string
    {
        if ($this->item_type instanceof WebmallItemTypeEnum) {
            return $this->item_type->getLabel();
        }

        return WebmallItemTypeEnum::tryFrom((string) $this->item_type)?->getLabel()
            ?? (string) $this->item_type;
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

    private function normalizeRefItemIdForType(): void
    {
        if ($this->isCustomItem()) {
            $this->ref_item_id = (int) ($this->ref_item_id ?? 0);

            return;
        }

        if (empty($this->ref_item_id)) {
            $this->ref_item_id = 0;
        }
    }
}

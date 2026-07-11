<?php

namespace App\Models;

use App\Enums\MarketListingStatusEnum;
use App\Enums\SilkTypeEnum;
use App\Enums\SilkTypeIsroEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $user_id
 * @property int $character_id
 * @property string $character_name
 * @property int $item_id64
 * @property int $ref_item_id
 * @property string $item_name
 * @property int $opt_level
 * @property int $quantity
 * @property array<string, mixed>|null $item_data
 * @property string $price_type
 * @property int $price_amount
 * @property string|null $fee_type
 * @property int $fee_amount
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property MarketListingStatusEnum $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class MarketListing extends Model
{
    protected $table = 'market_listings';

    protected $fillable = [
        'user_id',
        'character_id',
        'character_name',
        'item_id64',
        'ref_item_id',
        'item_name',
        'opt_level',
        'quantity',
        'item_data',
        'price_type',
        'price_amount',
        'fee_type',
        'fee_amount',
        'description',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'item_id64' => 'integer',
        'ref_item_id' => 'integer',
        'opt_level' => 'integer',
        'quantity' => 'integer',
        'price_amount' => 'integer',
        'fee_amount' => 'integer',
        'item_data' => 'array',
        'status' => MarketListingStatusEnum::class,
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transaction(): HasOne
    {
        return $this->hasOne(MarketTransaction::class, 'listing_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', MarketListingStatusEnum::ACTIVE);
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('status', MarketListingStatusEnum::ACTIVE)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now());
    }

    public function isActive(): bool
    {
        return $this->status === MarketListingStatusEnum::ACTIVE;
    }

    public function isExpired(): bool
    {
        return $this->status === MarketListingStatusEnum::ACTIVE
            && $this->expires_at !== null
            && $this->expires_at->isPast();
    }

    public function isGold(): bool
    {
        return $this->price_type === 'gold';
    }

    public function isSilk(): bool
    {
        return $this->price_type !== 'gold';
    }

    public function priceTypeLabel(): string
    {
        return match (true) {
            $this->price_type === 'gold' => 'Gold',
            SilkTypeIsroEnum::tryFrom((int) $this->price_type) !== null => SilkTypeIsroEnum::from((int) $this->price_type)->getLabel(),
            SilkTypeEnum::tryFrom((string) $this->price_type) !== null => SilkTypeEnum::from((string) $this->price_type)->getLabel(),
            default => $this->price_type,
        };
    }

    public function netAmount(): int
    {
        return max(0, $this->price_amount - $this->fee_amount);
    }

    public function getIconPathAttribute(): string
    {
        return \App\Helpers\WebmallItemIconHelper::iconPath($this->ref_item_id);
    }

    public function getRemainingTimeAttribute(): ?string
    {
        if (! $this->expires_at) {
            return null;
        }

        return $this->expires_at->diffForHumans();
    }
}

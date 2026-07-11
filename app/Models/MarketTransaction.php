<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $listing_id
 * @property int $seller_id
 * @property int $buyer_id
 * @property int $seller_character_id
 * @property int $buyer_character_id
 * @property string $seller_character_name
 * @property string $buyer_character_name
 * @property int $ref_item_id
 * @property string $item_name
 * @property int $opt_level
 * @property int $quantity
 * @property array<string, mixed>|null $item_data
 * @property string $price_type
 * @property int $price_amount
 * @property string|null $fee_type
 * @property int $fee_amount
 * @property int $net_amount
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class MarketTransaction extends Model
{
    protected $table = 'market_transactions';

    protected $fillable = [
        'listing_id',
        'seller_id',
        'buyer_id',
        'seller_character_id',
        'buyer_character_id',
        'seller_character_name',
        'buyer_character_name',
        'ref_item_id',
        'item_name',
        'opt_level',
        'quantity',
        'item_data',
        'price_type',
        'price_amount',
        'fee_type',
        'fee_amount',
        'net_amount',
    ];

    protected $casts = [
        'ref_item_id' => 'integer',
        'opt_level' => 'integer',
        'quantity' => 'integer',
        'price_amount' => 'integer',
        'fee_amount' => 'integer',
        'net_amount' => 'integer',
        'item_data' => 'array',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(MarketListing::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}

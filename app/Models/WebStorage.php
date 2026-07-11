<?php

namespace App\Models;

use App\Enums\WebStorageSourceTypeEnum;
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
 * @property string $source_type
 * @property int $opt_level
 * @property int $quantity
 * @property array<string, mixed>|null $item_data
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class WebStorage extends Model
{
    protected $table = 'web_storage';

    protected $fillable = [
        'user_id',
        'character_id',
        'character_name',
        'item_id64',
        'ref_item_id',
        'item_name',
        'source_type',
        'opt_level',
        'quantity',
        'item_data',
    ];

    protected $casts = [
        'item_id64' => 'integer',
        'ref_item_id' => 'integer',
        'opt_level' => 'integer',
        'quantity' => 'integer',
        'item_data' => 'array',
        'source_type' => WebStorageSourceTypeEnum::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function activeListing(): HasOne
    {
        return $this->hasOne(MarketListing::class, 'item_id64', 'item_id64')
            ->where('status', 'active');
    }

    public function isListed(): bool
    {
        return MarketListing::where('item_id64', $this->item_id64)
            ->where('status', 'active')
            ->exists();
    }

    public function getIconPathAttribute(): string
    {
        return \App\Helpers\WebmallItemIconHelper::iconPath($this->ref_item_id);
    }
}

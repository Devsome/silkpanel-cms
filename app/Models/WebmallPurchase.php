<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $character_id
 * @property string $character_name
 * @property int $webmall_category_item_id
 * @property int $ref_item_id
 * @property string $item_name
 * @property string $price_type
 * @property int $price_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class WebmallPurchase extends Model
{
    protected $table = 'webmall_purchases';

    protected $fillable = [
        'user_id',
        'character_id',
        'character_name',
        'webmall_category_item_id',
        'ref_item_id',
        'item_name',
        'price_type',
        'price_value',
    ];

    protected $casts = [
        'price_value' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categoryItem(): BelongsTo
    {
        return $this->belongsTo(WebmallCategoryItem::class, 'webmall_category_item_id')->withTrashed();
    }
}

<?php

namespace App\Models;

use App\Enums\WebmallItemTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $character_id
 * @property string $character_name
 * @property int $webmall_category_item_id
 * @property string $item_type
 * @property int $ref_item_id
 * @property string $item_name
 * @property string $price_type
 * @property int $price_value
 * @property string $status
 * @property int|null $procedure_mapping_id
 * @property int|null $procedure_log_id
 * @property string|null $procedure_name_snapshot
 * @property string|null $procedure_error_message
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
        'item_type',
        'ref_item_id',
        'item_name',
        'price_type',
        'price_value',
        'status',
        'procedure_mapping_id',
        'procedure_log_id',
        'procedure_name_snapshot',
        'procedure_error_message',
    ];

    protected $casts = [
        'price_value' => 'integer',
        'item_type' => WebmallItemTypeEnum::class,
        'procedure_mapping_id' => 'integer',
        'procedure_log_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categoryItem(): BelongsTo
    {
        return $this->belongsTo(WebmallCategoryItem::class, 'webmall_category_item_id')->withTrashed();
    }

    public function procedureMapping(): BelongsTo
    {
        return $this->belongsTo(ProcedureMapping::class, 'procedure_mapping_id');
    }

    public function procedureLog(): BelongsTo
    {
        return $this->belongsTo(ProcedureLog::class, 'procedure_log_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemLog extends Model
{
    protected $fillable = [
        'user_id',
        'char_id',
        'char_name',
        'procedure',
        'code_name',
        'ref_item_id',
        'data',
        'opt_level',
        'variance',
        'success',
        'return_code',
        'destination',
        'slot',
        'new_item_id',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'success'     => 'boolean',
            'return_code' => 'integer',
            'data'        => 'integer',
            'opt_level'   => 'integer',
            'variance'    => 'integer',
            'slot'        => 'integer',
            'new_item_id' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

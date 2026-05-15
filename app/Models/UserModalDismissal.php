<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserModalDismissal extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'session_modal_id',
        'dismissed_at',
    ];

    protected $casts = [
        'dismissed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sessionModal(): BelongsTo
    {
        return $this->belongsTo(SessionModal::class);
    }
}

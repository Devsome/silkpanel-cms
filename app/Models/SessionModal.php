<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class SessionModal extends Model
{
    protected $fillable = [
        'title',
        'content',
        'image',
        'buttons',
        'is_active',
        'frequency',
        'conditions',
        'starts_at',
        'ends_at',
        'allow_backdrop_dismiss',
        'sort_order',
    ];

    protected $casts = [
        'buttons' => 'array',
        'conditions' => 'array',
        'is_active' => 'boolean',
        'allow_backdrop_dismiss' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function dismissals(): HasMany
    {
        return $this->hasMany(UserModalDismissal::class);
    }

    public function isWithinDateRange(): bool
    {
        $now = Carbon::now();

        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->ends_at && $now->gt($this->ends_at)) {
            return false;
        }

        return true;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

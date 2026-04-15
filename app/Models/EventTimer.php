<?php

namespace App\Models;

use App\Services\EventTimerService;
use Illuminate\Database\Eloquent\Model;

class EventTimer extends Model
{
    protected $fillable = [
        'name',
        'type',
        'days',
        'hours',
        'hour',
        'min',
        'time',
        'icon',
        'image',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'days' => 'array',
            'hours' => 'array',
            'hour' => 'integer',
            'min' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function isStatic(): bool
    {
        return $this->type === 'static';
    }

    protected static function booted(): void
    {
        $clearCache = fn() => EventTimerService::clearCache();

        static::created($clearCache);
        static::updated($clearCache);
        static::deleted($clearCache);
    }
}

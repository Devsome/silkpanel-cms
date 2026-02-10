<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'path',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Activate this template and deactivate all others.
     */
    public function activate(): void
    {
        // Deactivate all templates
        static::query()->update(['is_active' => false]);
        
        // Activate this template
        $this->update(['is_active' => true]);
    }

    /**
     * Get the active template.
     */
    public static function getActive(): ?self
    {
        return static::where('is_active', true)->first();
    }
}

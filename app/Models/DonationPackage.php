<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DonationPackage extends Model
{
    protected $fillable = [
        'name',
        'description',
        'silk_amount',
        'silk_type',
        'price',
        'currency',
        'is_active',
        'sort_order',
        'image',
    ];

    protected function casts(): array
    {
        return [
            'silk_amount' => 'integer',
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function paymentProviders(): BelongsToMany
    {
        return $this->belongsToMany(PaymentProvider::class, 'donation_package_payment_provider');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

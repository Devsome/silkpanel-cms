<?php

namespace App\Models;

use App\Enums\PaymentProviderEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PaymentProvider extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'description',
        'is_active',
        'sort_order',
        'denomination_silks',
    ];

    protected function casts(): array
    {
        return [
            'slug' => PaymentProviderEnum::class,
            'is_active' => 'boolean',
            'denomination_silks' => 'array',
        ];
    }

    public function donationPackages(): BelongsToMany
    {
        return $this->belongsToMany(DonationPackage::class, 'donation_package_payment_provider');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

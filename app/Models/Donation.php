<?php

namespace App\Models;

use App\Enums\DonationStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    protected $fillable = [
        'user_id',
        'donation_package_id',
        'payment_provider_slug',
        'transaction_id',
        'amount',
        'currency',
        'silk_amount',
        'silk_type',
        'status',
        'payment_data',
        'ip_address',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'silk_amount' => 'integer',
            'status' => DonationStatusEnum::class,
            'payment_data' => 'array',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function donationPackage(): BelongsTo
    {
        return $this->belongsTo(DonationPackage::class);
    }

    public function isPending(): bool
    {
        return $this->status === DonationStatusEnum::PENDING;
    }

    public function isCompleted(): bool
    {
        return $this->status === DonationStatusEnum::COMPLETED;
    }
}

<?php

namespace App\Models;

use App\Enums\DatabaseNameEnums;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $referrer_id
 * @property int $referred_id
 * @property string $status  pending|valid
 * @property int $silk_rewarded
 * @property \Illuminate\Support\Carbon|null $rewarded_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $referrer
 * @property-read User $referred
 */
class Referral extends Model
{
    protected $connection = DatabaseNameEnums::MYSQL->value;

    protected $table = 'referrals';

    protected $fillable = [
        'referrer_id',
        'referred_id',
        'status',
        'silk_rewarded',
        'rewarded_at',
    ];

    protected $casts = [
        'rewarded_at' => 'datetime',
        'silk_rewarded' => 'integer',
    ];

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referred(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isValid(): bool
    {
        return $this->status === 'valid';
    }
}

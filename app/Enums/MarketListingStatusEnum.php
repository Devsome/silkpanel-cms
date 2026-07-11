<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum MarketListingStatusEnum: string implements HasLabel, HasColor
{
    case ACTIVE = 'active';
    case SOLD = 'sold';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::SOLD => 'Sold',
            self::EXPIRED => 'Expired',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::SOLD => 'info',
            self::EXPIRED => 'warning',
            self::CANCELLED => 'danger',
        };
    }
}

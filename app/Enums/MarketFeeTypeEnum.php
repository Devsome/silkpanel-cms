<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum MarketFeeTypeEnum: string implements HasLabel
{
    case PERCENT = 'percent';
    case FIXED = 'fixed';

    public function getLabel(): string
    {
        return match ($this) {
            self::PERCENT => 'Percentage (%)',
            self::FIXED => 'Fixed Amount',
        };
    }
}

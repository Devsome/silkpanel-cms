<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum WebmallItemTypeEnum: string implements HasLabel, HasColor
{
    case REGULAR_ITEM = 'regular_item';
    case CUSTOM_ITEM = 'custom_item';

    public function getLabel(): string
    {
        return match ($this) {
            self::REGULAR_ITEM => 'Regular Item',
            self::CUSTOM_ITEM => 'Custom Item',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::REGULAR_ITEM => 'gray',
            self::CUSTOM_ITEM => 'warning',
        };
    }
}

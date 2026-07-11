<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum WebStorageSourceTypeEnum: string implements HasLabel
{
    case INVENTORY = 'inventory';
    case STORAGE = 'storage';

    public function getLabel(): string
    {
        return match ($this) {
            self::INVENTORY => 'Inventory',
            self::STORAGE => 'Storage',
        };
    }
}

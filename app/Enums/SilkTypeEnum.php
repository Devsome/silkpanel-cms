<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum SilkTypeEnum: string implements HasLabel
{
    case SILK_OWN = 'silk_own';
    case SILK_GIFT = 'silk_gift';
    case SILK_POINT = 'silk_point';

    public function getLabel(): string | Htmlable | null
    {
        return match ($this) {
            static::SILK_OWN => 'Silk Own',
            static::SILK_GIFT => 'Silk Gift',
            static::SILK_POINT => 'Silk Point',
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}

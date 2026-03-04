<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum SilkTypeIsroEnum: int implements HasLabel
{
    case SILK_TYPE_NORMAL = 1;
    case SILK_TYPE_PREMIUM = 3;

    public function getLabel(): string | Htmlable | null
    {
        return match ($this) {
            static::SILK_TYPE_NORMAL => 'Normal Silk',
            static::SILK_TYPE_PREMIUM => 'Premium Silk',
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}

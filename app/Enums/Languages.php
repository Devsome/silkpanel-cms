<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum Languages: string implements HasLabel
{
    case EN = 'en';
    case DE = 'de';
    case TR = 'tr';
    case FR = 'fr';
    case ES = 'es';
    case IT = 'it';
    case PT = 'pt';
    case AR = 'ar';

    public function getLabel(): string | Htmlable | null
    {
        return match ($this) {
            static::EN => 'English',
            static::DE => 'German',
            static::TR => 'Turkish',
            static::FR => 'French',
            static::ES => 'Spanish',
            static::IT => 'Italian',
            static::PT => 'Portuguese',
            static::AR => 'Arabic',
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}

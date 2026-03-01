<?php

namespace App\Enums;

enum UsergroupRoleEnums: string
{
    case ADMIN = 'Administrator';
    case SUPPORTER = 'Supporter';
    case CUSTOMER = 'Customer';

    public function label(): string
    {
        return match ($this) {
            static::ADMIN => 'Administrator',
            static::SUPPORTER => 'Supporter',
            static::CUSTOMER => 'Customer',
        };
    }
}

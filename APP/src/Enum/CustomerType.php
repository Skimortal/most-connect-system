<?php

namespace App\Enum;

enum CustomerType: string
{
    case PRIVATE = 'private';
    case BUSINESS = 'business';
    case NON_PROFIT = 'non_profit';
    case GOVERNMENT = 'government';
    case INTERNAL = 'internal';

    public function label(): string
    {
        return match($this) {
            self::PRIVATE => 'Privatkunde',
            self::BUSINESS => 'Geschäftskunde',
            self::NON_PROFIT => 'Gemeinnützig',
            self::GOVERNMENT => 'Behörde',
            self::INTERNAL => 'Intern',
        };
    }
}

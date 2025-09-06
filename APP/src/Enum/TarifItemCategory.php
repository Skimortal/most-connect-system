<?php
namespace App\Enum;

enum TarifItemCategory: string
{
    case HAUSHALT = 'Haushalt';
    case GEWERBE = 'Gewerbe';

    public function label(): string
    {
        return match($this) {
            self::HAUSHALT     => 'Haushalt',
            self::GEWERBE     => 'Gewerbe',
        };
    }
}

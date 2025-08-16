<?php
namespace App\Enum;

enum InvoiceStatus: string
{
    case OFFEN     = 'offen';
    case VERSENDET = 'versendet';
    case BEZAHLT   = 'bezahlt';

    public function label(): string
    {
        return match($this) {
            self::OFFEN     => 'Offen',
            self::VERSENDET => 'Versendet',
            self::BEZAHLT   => 'Bezahlt',
        };
    }
}

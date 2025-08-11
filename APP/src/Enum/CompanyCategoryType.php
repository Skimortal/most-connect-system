<?php
namespace App\Enum;

enum CompanyCategoryType: string
{
    case IT = 'it';
    case FINANCE = 'finance';
    case HEALTHCARE = 'healthcare';
    case EDUCATION = 'education';
    case MANUFACTURING = 'manufacturing';
    case RETAIL = 'retail';
    case LOGISTICS = 'logistics';
    case MARKETING = 'marketing';
    case CONSTRUCTION = 'construction';
    case ENERGY = 'energy';

    public function label(): string
    {
        return match($this) {
            self::IT => 'IT & Software',
            self::FINANCE => 'Finanzen & Versicherungen',
            self::HEALTHCARE => 'Gesundheitswesen',
            self::EDUCATION => 'Bildung & Forschung',
            self::MANUFACTURING => 'Produktion & Industrie',
            self::RETAIL => 'Einzelhandel',
            self::LOGISTICS => 'Logistik & Transport',
            self::MARKETING => 'Marketing & Werbung',
            self::CONSTRUCTION => 'Bauwesen',
            self::ENERGY => 'Energie & Umwelt',
        };
    }
}

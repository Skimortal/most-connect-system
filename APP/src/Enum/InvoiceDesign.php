<?php
namespace App\Enum;

enum InvoiceDesign: string
{
    case MINIMAL    = 'minimal';
    case MODERN     = 'modern';
    case LETTERHEAD = 'letterhead';
    case GRID       = 'grid';
    case ELEGANT    = 'elegant';
}

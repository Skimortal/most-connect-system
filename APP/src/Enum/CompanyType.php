<?php

namespace App\Enum;

enum CompanyType: string
{
    case EINZELUNTERNEHMEN = 'Einzelunternehmen';
    case GMBH = 'GmbH';
    case KG = 'KG';
    case OG = 'OG';
    case AG = 'AG';
    case VEREIN = 'Verein';
    case GMBH_CO_KG = 'GmbH & Co. KG';
}

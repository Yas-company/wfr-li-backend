<?php

namespace App\Enums\Tax;

use App\Traits\HasLabel;

enum TaxGroup: string
{
    use HasLabel;

    case PLATFORM = 'platform';
    case COUNTRY_VAT = 'country vat';
    case OTHER = 'other';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

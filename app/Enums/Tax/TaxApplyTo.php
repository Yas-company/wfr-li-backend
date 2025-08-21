<?php

namespace App\Enums\Tax;

use App\Traits\HasLabel;

enum TaxApplyTo: string
{
    use HasLabel;

    case PRODUCT = 'product';
    case ORDER = 'order';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

<?php

namespace App\Enums\Tax;

use App\Traits\HasLabel;
use App\Traits\HasOptions;

enum TaxApplyTo: string
{
    use HasLabel, HasOptions;

    case PRODUCT = 'product';
    case ORDER = 'order';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

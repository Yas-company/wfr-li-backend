<?php

namespace App\Enums;

use App\Traits\HasLabel;

enum ProductStatus: int
{
    use HasLabel;

    case DRAFT = 0;
    case PUBLISHED = 1;
    case REJECTED = 2;

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

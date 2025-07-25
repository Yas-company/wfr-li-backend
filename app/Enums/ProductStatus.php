<?php

namespace App\Enums;

enum ProductStatus: int
{
    case DRAFT = 0;
    case PUBLISHED = 1;
    case REJECTED = 2;

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

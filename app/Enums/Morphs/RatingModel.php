<?php

namespace App\Enums\Morphs;

enum RatingModel: string
{
    case PRODUCT = 'product';
    case ORDER = 'order';
    case USER = 'user';

    public static function getMorphClasses(): array
    {
        return array_column(self::cases(), 'value');
    }
}

<?php

namespace App\Enums\Morphs;

use App\Traits\HasLabel;

enum RatingModel: string
{
    use HasLabel;

    case PRODUCT = 'product';
    case ORDER = 'order';
    case USER = 'user';

    public static function getMorphClasses(): array
    {
        return array_column(self::cases(), 'value');
    }
}

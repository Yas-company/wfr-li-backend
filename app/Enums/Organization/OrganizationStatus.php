<?php

namespace App\Enums\Organization;

use App\Traits\HasLabel;

enum OrganizationStatus: int
{
    use HasLabel;

    case PENDING = 1;
    case APPROVED = 2;
    case REJECTED = 3;

    public static function colors(): array
    {
        return [
            'gray' => self::PENDING->value,
            'success' => self::APPROVED->value,
            'danger' => self::REJECTED->value,
        ];
    }
}

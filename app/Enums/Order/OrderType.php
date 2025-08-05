<?php

namespace App\Enums\Order;

use App\Traits\HasLabel;

enum OrderType: int
{
    use HasLabel;

    case INDIVIDUAL = 1;
    case ORGANIZATION = 2;
}

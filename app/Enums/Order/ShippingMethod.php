<?php

namespace App\Enums\Order;

use App\Traits\HasLabel;

enum ShippingMethod: int
{
    use HasLabel;

    case DELEGATE = 1;
    case PICKUP = 2;
}

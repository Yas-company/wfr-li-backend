<?php

namespace App\Enums\Order;

enum ShippingMethod: int
{
    case DELEGATE = 1;
    case PICKUP = 2;
}

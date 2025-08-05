<?php

namespace App\Enums\Order;

enum OrderType: int
{
    case INDIVIDUAL = 1;
    case ORGANIZATION = 2;
}

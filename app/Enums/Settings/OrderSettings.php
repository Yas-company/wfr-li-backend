<?php

namespace App\Enums\Settings;

use App\Traits\HasLabel;

enum OrderSettings: string
{
    use HasLabel;

    case ORDER_MIN_ORDER_AMOUNT = 'order.min_order_amount';
}

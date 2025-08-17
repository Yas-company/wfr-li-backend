<?php

namespace App\Enums\Payment;

use App\Traits\HasLabel;

enum PaymentGateway: string
{
    use HasLabel;

    case TAP = 'tap';
}

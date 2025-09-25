<?php

namespace App\Enums\Payment\Tap;

use App\Traits\HasLabel;

enum TapPaymentStatus: string
{
    use HasLabel;

    case CAPTURED = 'CAPTURED';
    case FAILED = 'FAILED';
}

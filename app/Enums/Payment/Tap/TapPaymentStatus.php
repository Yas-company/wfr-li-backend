<?php

namespace App\Enums\Payment\Tap;

use App\Traits\HasLabel;

enum TapPaymentStatus: string
{
    use HasLabel;

    case PENDING = 'pending';
    case CAPTURED = 'captured';
    case FAILED = 'failed';
}

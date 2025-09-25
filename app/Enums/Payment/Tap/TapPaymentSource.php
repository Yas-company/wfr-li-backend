<?php

namespace App\Enums\Payment\Tap;

use App\Traits\HasLabel;

enum TapPaymentSource: string
{
    use HasLabel;

    case ALL = 'src_all';
    case MADA = 'src_sa.mada';
    case CARD = 'src_card';
}

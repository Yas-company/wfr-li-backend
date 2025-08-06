<?php

namespace App\Enums;

use App\Traits\HasLabel;

enum TapPaymentMethod: string
{
    use HasLabel;
    case ALL = 'src_all';
    case MADA = 'src_sa.mada';
    case CARD = 'src_card';
    case APPLE_PAY = 'src_applepay';
}

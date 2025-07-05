<?php

namespace App\Enums;

enum TapPaymentMethod: string
{
    case ALL = 'src_all';
    case MADA = 'src_sa.mada';
    case CARD = 'src_card';
    case APPLE_PAY = 'src_applepay';
}

<?php

namespace App\Enums\Order;

use App\Traits\HasLabel;

enum PaymentMethod: int
{
    use HasLabel;

    case CASH_ON_DELIVERY = 1;
    case Tap = 2;

    public function color(): string
    {
        return match($this) {
            self::CASH_ON_DELIVERY => 'success',
            self::Tap => 'primary',
        };
    }
}

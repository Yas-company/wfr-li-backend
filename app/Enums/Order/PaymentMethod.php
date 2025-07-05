<?php

namespace App\Enums\Order;

enum PaymentMethod: int
{
    case CASH_ON_DELIVERY = 1;
    case Tap = 2;

    public function label(): string
    {
        return match($this) {
            self::CASH_ON_DELIVERY => 'cash_on_delivery',
            self::Tap => 'Tap',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::CASH_ON_DELIVERY => 'success',
            self::Tap => 'primary',
        };
    }
}

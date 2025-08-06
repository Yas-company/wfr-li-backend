<?php

namespace App\Enums\Order;

use App\Traits\HasLabel;

enum PaymentStatus: int
{
    use HasLabel;

    case PENDING = 1;
    case PAID = 2;
    case CANCELLED = 3;

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::PAID => 'success',
            self::CANCELLED => 'danger',
        };
    }
}

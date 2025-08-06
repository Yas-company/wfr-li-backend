<?php

namespace App\Enums\Order;

use App\Traits\HasLabel;

enum OrderStatus: string
{
    use HasLabel;

    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case PAID = 'paid';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::ACCEPTED => 'info',
            self::REJECTED => 'danger',
            self::PAID => 'success',
            self::SHIPPED => 'primary',
            self::DELIVERED => 'success',
            self::CANCELLED => 'danger',
        };
    }
}

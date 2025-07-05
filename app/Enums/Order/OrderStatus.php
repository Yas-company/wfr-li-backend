<?php

namespace App\Enums\Order;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case PAID = 'paid';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::ACCEPTED => 'Accepted',
            self::REJECTED => 'Rejected',
            self::PAID => 'Paid',
            self::SHIPPED => 'Shipped',
            self::DELIVERED => 'Delivered',
            self::CANCELLED => 'Cancelled',
        };
    }

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

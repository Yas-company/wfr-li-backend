<?php

namespace App\Enums\Order;

use App\Traits\HasLabel;

enum PaymentStatus: int
{
    use HasLabel;

    case PENDING = 1; // pending
    case PAID = 2; // paid;
    case CANCELLED = 3; //failed;

    // public function label(): string
    // {
    //     return match($this) {
    //         self::PENDING => 'Pending',
    //         self::PAID => 'Paid',
    //         self::CANCELLED => 'Failed',
    //     };
    // }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::PAID => 'success',
            self::CANCELLED => 'danger',
        };
    }
}

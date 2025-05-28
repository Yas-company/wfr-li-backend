<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case CASH = 'cash';
    case VISA = 'visa';

    public function label(): string
    {
        return match($this) {
            self::CASH => 'Cash',
            self::VISA => 'Visa',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::CASH => 'success',
            self::VISA => 'primary',
        };
    }
} 
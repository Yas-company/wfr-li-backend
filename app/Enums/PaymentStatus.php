<?php

namespace App\Enums;

enum PaymentStatus: int
{
    case PENDING = 1; // pending
    case PAID = 2; // paid;
    case FAILED = 3; //failed;
}

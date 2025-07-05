<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\PaymentStatus;

class Payment extends Model
{
    protected $fillable = [
        'tap_id',
        'reference_id',
        'payment_method',
        'status',
        'amount',
        'currency',
        'user_id'
    ];

    protected $casts = [
        'status' => PaymentStatus::class,
    ];
}

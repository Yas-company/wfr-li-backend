<?php

namespace App\Models;

use App\Enums\Order\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

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

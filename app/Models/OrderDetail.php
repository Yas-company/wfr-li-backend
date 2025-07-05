<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = [
        'order_id',
        'shipping_address',
        'shipping_latitude',
        'shipping_longitude',
        'payment_status',
        'payment_method',
        'tracking_number',
        'estimated_delivery_date',
        'notes',
    ];
}

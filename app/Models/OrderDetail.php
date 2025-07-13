<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'shipping_address_id',
        'payment_status',
        'payment_method',
        'tracking_number',
        'estimated_delivery_date',
        'notes',
        'shipping_method',
    ];

    protected $with = ['shippingAddress'];

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }
}

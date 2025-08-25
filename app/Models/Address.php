<?php

namespace App\Models;

use App\Policies\AddressPolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'street',
        'city',
        'phone',
        'latitude',
        'longitude',
        'is_default',
        'user_id',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function isDefault(): bool
    {
        return $this->is_default;
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'shipping_address_id');
    }

    public function orders()
    {
        return $this->hasManyThrough(Order::class, OrderDetail::class, 'shipping_address_id', 'id', 'id', 'order_id');
    }
}

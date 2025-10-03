<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservedStock extends Model
{
    protected $table = 'reserved_stock';

    protected $fillable = ['order_id', 'product_id', 'quantity', 'reserved_at', 'expires_at'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

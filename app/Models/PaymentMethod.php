<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = ['name_ar', 'name_en', 'code', 'is_active'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}

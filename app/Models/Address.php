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
}

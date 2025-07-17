<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferNotification extends Model
{
    protected $fillable = ['name', 'description', 'offer_date', 'is_active'];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'is_active' => 'boolean',
        'offer_date' => 'date',
    ];

    // Accessor for English name
    public function getNameEnAttribute()
    {
        return $this->name['en'] ?? '';
    }

    // Accessor for Arabic name
    public function getNameArAttribute()
    {
        return $this->name['ar'] ?? '';
    }

    // Accessor for English description
    public function getDescEnAttribute()
    {
        return $this->description['en'] ?? '';
    }

    // Accessor for Arabic description
    public function getDescArAttribute()
    {
        return $this->description['ar'] ?? '';
    }
}

<?php

namespace App\Models;

use App\Enums\Tax\TaxApplyTo;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $fillable = [
        'name',
        'code',
        'rate',
        'is_active',
        'group',
        'applies_to',
    ];

    public function scopeForProducts()
    {
        return $this->where('applies_to', TaxApplyTo::PRODUCT->value);
    }

    public function scopeActive()
    {
        return $this->where('is_active', true);
    }
}

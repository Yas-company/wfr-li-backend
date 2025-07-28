<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use HasFactory, HasTranslations;

    protected $guarded = [];

    protected $casts = [
        'name' => 'array',
    ];

    // Relationships
    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function supplier()
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    public function countProducts()
    {
        return $this->products()->count();
    }
}

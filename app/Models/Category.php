<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use HasTranslations, HasFactory;

    protected $guarded = ['id'];

    public $translatable = ['name'];

    public $casts = [
        'name' => 'array',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    public function countProducts()
    {
        return $this->products()->count();
    }
}

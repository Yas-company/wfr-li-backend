<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasFactory;
    use HasTranslations;

    protected $guarded = [];

    public $translatable = ['name', 'description'];

    protected $casts = [
        'name' => 'array',
        'price' => 'decimal:2',
        'stock_qty' => 'integer',
        'price_before_discount' => 'decimal:2',
        'description' => 'array'
    ];

    protected $with = ['category'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function factory()
    {
        return $this->belongsTo(Factory::class);
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'product_supplier')
            ->withTimestamps();
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return asset('images/logo.jpeg');
        }

        return asset('storage/' . $this->image);
    }
}

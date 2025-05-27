<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Translatable\HasTranslations;

class Supplier extends Model
{
    use HasFactory;
    use HasTranslations;
    use HasApiTokens;

    protected $fillable = [
        'name',
        'phone',
        'address',
        'latitude',
        'longitude',
        'factory_id',
        'email',
        'password',
        'is_verified',
    ];

    public $translatable = ['name'];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    public function factory(): BelongsTo
    {
        return $this->belongsTo(Factory::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_supplier')
            ->withTimestamps();
    }
}

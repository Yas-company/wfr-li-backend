<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;
class Supplier extends Model
{
    use HasFactory;
    use HasTranslations;

    protected $fillable = [
        'name',
        'phone',
        'address',
        'location',
        'factory_id',
        'email',
        'password',
        'is_verified',
    ];

    public $translatable = ['name', 'address'];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
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

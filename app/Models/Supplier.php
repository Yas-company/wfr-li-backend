<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_supplier')
            ->withTimestamps();
    }

    public static function extractLatLngFromLink($link)
    {
        // Google Maps
        if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $link, $matches)) {
            return [$matches[1], $matches[2]];
        } elseif (preg_match('/\/place\/(-?\d+\.\d+),(-?\d+\.\d+)/', $link, $matches)) {
            return [$matches[1], $matches[2]];
        }
        // Apple Maps
        elseif (preg_match('/coordinate=([\d\.\-]+),([\d\.\-]+)/', $link, $matches)) {
            return [$matches[1], $matches[2]];
        }
        return [null, null];
    }
}

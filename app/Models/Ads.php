<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;


class Ads extends Model
{
    use HasFactory;
    use HasTranslations;

    public $translatable = ['title'];

    protected $casts = [
        'title' => 'array',
        'is_active' => 'boolean',
    ];

    protected $fillable = ['title', 'image', 'supplier_id', 'is_active'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return asset('images/logo.jpeg');
        }

        return asset('storage/' . $this->image);
    }

}
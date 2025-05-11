<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class OnboardingScreen extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'image',
        'title',
        'description',
        'order',
        'is_active',
    ];

    public $translatable = ['title', 'description'];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    protected $attributes = [
        'is_active' => false,
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return asset('images/logo.jpeg');
        }

        return asset('storage/' . $this->image);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Field extends Model
{
    use HasFactory;
    use HasTranslations;

    protected $fillable = [
        'name',
        'image'
    ];

    public $translatable = ['name'];

    protected $casts = [
        'name' => 'array',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }


    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return asset('images/logo.jpeg');
        }

        return asset('storage/' . $this->image);
    }

} 
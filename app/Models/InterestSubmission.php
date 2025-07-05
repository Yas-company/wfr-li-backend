<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterestSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'business_type',
        'city',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Accessor for business type display
    public function getBusinessTypeDisplayAttribute()
    {
        return match($this->business_type) {
            'restaurant' => 'مطعم',
            'cafe' => 'كافيه',
            'grocery' => 'بقالة',
            'supermarket' => 'سوبر ماركت',
            'catering' => 'خدمات الطعام',
            'other' => 'أخرى',
            default => $this->business_type
        };
    }

    // Accessor for city display
    public function getCityDisplayAttribute()
    {
        return match($this->city) {
            'makkah' => 'مكة المكرمة',
            'jeddah' => 'جدة',
            'riyadh' => 'الرياض',
            'dammam' => 'الدمام',
            'medina' => 'المدينة المنورة',
            'other' => 'مدينة أخرى',
            default => $this->city
        };
    }
} 
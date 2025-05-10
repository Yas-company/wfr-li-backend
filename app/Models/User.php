<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\UserRole;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are
     *  mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'phone',
        'address',
        'location',
        'business_name',
        'lic_id',
        'email',
        'password',
        'role',
        'is_verified',
        'otp',
        'otp_expiry',
        'country_code'
    ];

    protected $attributes = [
        'is_verified' => false,
        'role' => UserRole::VISITOR->value
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => UserRole::class,
        'is_verified' => 'boolean',
        'otp_expiry' => 'datetime'
    ];

    public function isVisitor(): bool
    {
        return $this->role === UserRole::VISITOR;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isBuyer(): bool
    {
        return $this->role === UserRole::BUYER;
    }
    public function isVerified(): bool
    {
        return $this->is_verified;
    }

    public function hasValidOtp(): bool
    {
        return $this->otp && $this->otp_expiry && $this->otp_expiry->isFuture();
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\UserRole;
use App\Enums\UserStatus;
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
        'latitude',
        'longitude',
        'business_name',
        'email',
        'password',
        'role',
        'is_verified',
        'status',
        'license_attachment',
        'commercial_register_attachment',
        'field_id',
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
        'status' => UserStatus::class,
        'is_verified' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
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

    public function isSupplier(): bool
    {
        return $this->role === UserRole::SUPPLIER;
    }

    public function isVerified(): bool
    {
        return $this->is_verified;
    }

    public function isApproved(): bool
    {
        return $this->status === UserStatus::APPROVED;
    }

    public function isPending(): bool
    {
        return $this->status === UserStatus::PENDING;
    }

    public function isRejected(): bool
    {
        return $this->status === UserStatus::REJECTED;
    }

    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function favoriteProducts()
    {
        return $this->belongsToMany(Product::class, 'favorites')->withTimestamps();
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_supplier')
            ->withTimestamps();
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'supplier_id');
    }
}

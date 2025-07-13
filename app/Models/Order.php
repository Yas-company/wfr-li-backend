<?php

namespace App\Models;

use App\Enums\Order\OrderStatus;
use App\Traits\Rateable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    use Rateable, HasFactory;

    protected $fillable = [
        'user_id',
        'total',
        'total_discount',
        'status',
        'supplier_id'
    ];


    protected $casts = [
        'status' => OrderStatus::class
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function orderDetail(): HasOne
    {
        return $this->hasOne(OrderDetail::class);
    }

    public function scopeForBuyer(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForSupplier(Builder $query, int $userId): Builder
    {
        return $query->where('supplier_id', $userId);
    }
}

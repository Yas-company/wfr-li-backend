<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'status',
        'total_amount',
        'shipping_address',
        'shipping_latitude',
        'shipping_longitude',
        'notes',
        'payment_status',
        'payment_method',
        'payment_id',
        'tracking_number',
        'estimated_delivery_date',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'shipping_latitude' => 'decimal:8',
        'shipping_longitude' => 'decimal:8',
        'estimated_delivery_date' => 'datetime',
        'payment_method' => PaymentMethod::class,
    ];

    protected $attributes = [
        'payment_method' => PaymentMethod::CASH,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeCashPayment($query)
    {
        return $query->where('payment_method', PaymentMethod::CASH);
    }

    public function scopeVisaPayment($query)
    {
        return $query->where('payment_method', PaymentMethod::VISA);
    }

    public function scopeFilterByStatus($query, $status)
    {
        return $query->when($status, fn($q) => $q->where('status', $status));
    }

    public function scopeFilterByPaymentStatus($query, $paymentStatus)
    {
        return $query->when($paymentStatus, fn($q) => $q->where('payment_status', $paymentStatus));
    }

    public function scopeFilterByPaymentMethod($query, $paymentMethod)
    {
        return $query->when($paymentMethod, fn($q) => $q->where('payment_method', $paymentMethod));
    }

    public function scopeFilterByDateRange($query, $startDate, $endDate)
    {
        return $query->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
                    ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate));
    }

    public function scopeFilterByAmountRange($query, $minAmount, $maxAmount)
    {
        return $query->when($minAmount, fn($q) => $q->where('total_amount', '>=', $minAmount))
                    ->when($maxAmount, fn($q) => $q->where('total_amount', '<=', $maxAmount));
    }

    public function scopeSortBy($query, $sortBy, $sortDirection)
    {
        return $query->orderBy($sortBy ?? 'created_at', $sortDirection ?? 'desc');
    }
} 
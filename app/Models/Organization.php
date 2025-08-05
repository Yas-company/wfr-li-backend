<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\Organization\OrganizationStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tax_number',
        'commercial_register_number',
        'created_by',
        'status'
    ];

    protected $casts = [
        'status' => OrganizationStatus::class,
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organization_user')->withPivot(['role', 'joined_at']);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', OrganizationStatus::APPROVED);
    }
}

<?php

namespace App\Repositories\Suppliers;

use App\Models\User;
use App\Enums\Order\OrderStatus;
use Illuminate\Support\Facades\DB;
use App\Enums\Organization\OrganizationStatus;
use Illuminate\Contracts\Database\Eloquent\Builder;

class BuyerRepository
{
    public function getRelatedBuyers(array $filters, User $supplier)
    {
        $query = $this->buildBaseQuery($supplier);

        $this->applySearch($query, $filters);
        $this->applySorting($query, $filters);

        return $query->groupBy('u.id', 'u.name')
            ->paginate(10);
    }

    protected function buildBaseQuery($supplier): Builder
    {
        $ordersSubquery = DB::table('orders')
            ->select([
                'user_id',
                DB::raw('SUM(total_products) as total_quantity'),
                DB::raw('SUM(total) as total_price'),
            ])
            ->where('supplier_id', $supplier->id)
            ->where('status', OrderStatus::DELIVERED->value)
            ->groupBy('user_id');

        return User::query()
            ->from('users as u')
            ->select([
                'u.id',
                'u.name',
                DB::raw('COALESCE(o.total_quantity, 0) as total_quantity'),
                DB::raw('COALESCE(o.total_price, 0) as total_price'),
                DB::raw('MAX(CASE WHEN org.id IS NOT NULL THEN 1 ELSE 0 END) as is_organization'),
            ])
            ->joinSub($ordersSubquery, 'o', function ($join) {
                $join->on('u.id', '=', 'o.user_id');
            })
            ->leftJoin('organizations as org', function ($join) {
                $join->on('org.created_by', '=', 'u.id')
                    ->where('org.status', OrganizationStatus::APPROVED->value);
            });
    }

    protected function applySearch(Builder $query, array $filters): void
    {
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('u.name', 'like', "$search%");
        }
    }

    protected function applySorting(Builder $query, array $filters): void
    {
        $sortBy = $filters['sort_by'] ?? 'total_price';
        $sortOrder = $filters['sort_order'] ?? 'asc';

        match ($sortBy) {
            'quantity' => $query->orderByRaw("COALESCE(SUM(o.total_quantity), 0) $sortOrder"),
            'total_price' => $query->orderByRaw("COALESCE(SUM(o.total_price), 0) $sortOrder"),
            default => $query->orderBy('u.name', $sortOrder),
        };
    }
}

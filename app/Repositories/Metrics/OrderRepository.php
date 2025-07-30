<?php

namespace App\Repositories\Metrics;

use App\Enums\Order\OrderStatus;
use Illuminate\Support\Facades\DB;

class OrderRepository
{
    /**
     * Get supplier order summaries.
     *
     * @param int $supplierId
     *
     */
    public function getSummarizedMetrics(int $supplierId)
    {
        return DB::table('orders')
                ->select([
                    DB::raw('COUNT(CASE WHEN status = ? THEN 1 END) AS count_pending_orders'),
                    DB::raw('COUNT(CASE WHEN status = ? THEN 1 END) AS count_delivered_orders'),
                    DB::raw('COUNT(CASE WHEN status = ? THEN 1 END) AS count_cancelled_orders'),
                    DB::raw('COALESCE(SUM(CASE WHEN status = ? THEN total ELSE 0 END), 0) AS total_sales'),
                ])
                ->addBinding([
                    OrderStatus::PENDING->value,
                    OrderStatus::DELIVERED->value,
                    OrderStatus::CANCELLED->value,
                    OrderStatus::DELIVERED->value,
                ])
                ->where('supplier_id', $supplierId)
                ->first();
    }
}

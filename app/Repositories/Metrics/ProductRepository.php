<?php

namespace App\Repositories\Metrics;

use Illuminate\Support\Facades\DB;

class ProductRepository
{

    /**
     * Get supplier product summaries.
     *
     * @param int $supplierId
     *
     */
    public function getSummarizedMetrics(int $supplierId)
    {
        return DB::table('products')
                ->select([
                    DB::raw('COUNT(id) AS count_all_products'),
                    DB::raw('COUNT(CASE WHEN stock_qty > ? THEN 1 END) AS count_available_products'),
                    DB::raw('COUNT(CASE WHEN stock_qty = ? THEN 1 END) AS count_out_of_stock_products'),
                    DB::raw('COUNT(CASE WHEN stock_qty > ? AND stock_qty <= ? THEN 1 END) AS count_nearly_out_of_stock_products'),
                ])
                ->addBinding([5, 0, 0, 5]) // TODO: Move this to config
                ->where('supplier_id', $supplierId)
                ->first();
    }
}

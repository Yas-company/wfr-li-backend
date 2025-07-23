<?php

namespace App\Services\Metrics\Supplier;

use App\Repositories\Metrics\OrderRepository;
use App\Repositories\Metrics\ProductRepository;
use Illuminate\Support\Facades\Cache;

class GeneralMetricsService
{
    /**
     * GeneralMetricsService constructor.
     *
     * @param OrderRepository $orderRepository
     * @param ProductRepository $productRepository
     */
    public function __construct(
        protected OrderRepository $orderRepository,
        protected ProductRepository $productRepository
    ) {
        //
    }

    /**
     * Get supplier general metrics.
     *
     * @param int $supplierId
     *
     * @return array
     */
    public function getMetrics(int $supplierId): array
    {
        return Cache::remember("supplier.metrics.{$supplierId}", now()->addMinutes(30), function () use ($supplierId) {
            $orderSummaries = $this->orderRepository->getSummarizedMetrics($supplierId);
            $productSummaries = $this->productRepository->getSummarizedMetrics($supplierId);

            return [
                'orders' => $orderSummaries,
                'products' => $productSummaries,
            ];
        });
    }
}

<?php

namespace App\Http\Controllers\api\v1\Metrics;

use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Metrics\Supplier\GeneralMetricsService;

class SupplierGeneralMetricsController extends Controller
{
    use ApiResponse;

    /**
     * SupplierGeneralMetricsController constructor.
     *
     * @param GeneralMetricsService $generalMetricsService
     */
    public function __construct(protected GeneralMetricsService $generalMetricsService)
    {
        //
    }

    /**
     * Get supplier general metrics.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMetrics(): JsonResponse
    {
        $supplierId = auth()->user()->id;
        $metrics = $this->generalMetricsService->getMetrics($supplierId);

        return $this->successResponse($metrics);
    }
}

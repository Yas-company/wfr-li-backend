<?php

namespace App\Http\Controllers\api\v1\Metrics;

use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Metrics\Supplier\GeneralMetricsService;
use OpenApi\Attributes as OA;
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
     * 
     * @OA\Get(
     *     path="/supplier/general-metrics",
     *     summary="Get supplier general metrics",
     *     description="Retrieves comprehensive general metrics and statistics for the authenticated supplier user. This endpoint provides key performance indicators including order counts, revenue data, product statistics, and other relevant business metrics.",
     *     tags={"Metrics"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved supplier metrics",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Metrics retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Supplier metrics data",
     *                 @OA\Property(
     *                     property="orders",
     *                     type="object",
     *                     description="Order-related metrics",
     *                     @OA\Property(property="total_orders", type="integer", example=150),
     *                     @OA\Property(property="pending_orders", type="integer", example=25),
     *                     @OA\Property(property="completed_orders", type="integer", example=100),
     *                     @OA\Property(property="cancelled_orders", type="integer", example=5),
     *                     @OA\Property(property="total_revenue", type="number", format="float", example=15000.50),
     *                     @OA\Property(property="average_order_value", type="number", format="float", example=100.00)
     *                 ),
     *                 @OA\Property(
     *                     property="products",
     *                     type="object",
     *                     description="Product-related metrics",
     *                     @OA\Property(property="total_products", type="integer", example=75),
     *                     @OA\Property(property="active_products", type="integer", example=65),
     *                     @OA\Property(property="inactive_products", type="integer", example=10),
     *                     @OA\Property(property="low_stock_products", type="integer", example=8)
     *                 )
     *             ),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing authentication token",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated"),
     *             @OA\Property(property="status_code", type="integer", example=401)
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - User does not have supplier role",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Access denied. Supplier role required."),
     *             @OA\Property(property="status_code", type="integer", example=403)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found - Supplier not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Supplier not found"),
     *             @OA\Property(property="status_code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred while retrieving metrics"),
     *             @OA\Property(property="status_code", type="integer", example=500)
     *         )
     *     ),
     *     @OA\Tag(name="Metrics", description="API endpoints for retrieving various metrics and statistics"),
     *     @OA\Tag(name="Supplier", description="API endpoints specific to supplier operations")
     * )
     */
    public function getMetrics(): JsonResponse
    {
        $supplierId = auth()->user()->id;
        $metrics = $this->generalMetricsService->getMetrics($supplierId);

        return $this->successResponse($metrics);
    }
}

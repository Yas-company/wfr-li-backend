<?php

namespace App\Http\Controllers\api\v1\Order\Supplier;

use App\Dtos\OrderFilterDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\FilterOrderRequest;
use App\Http\Resources\Order\SupplierOrderListingResource;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\Order\OrderService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(
 *     name="Supplier Orders",
 *     description="Supplier order management endpoints"
 * )
 */
class OrderController extends Controller
{
    use ApiResponse;

    /**
     * SupplierOrderController constructor.
     */
    public function __construct(protected OrderService $orderService)
    {
        //
    }

    /**
     * Display a listing of the user's orders.
     *
     * @OA\Get(
     *     path="/supplier/orders",
     *     summary="Get supplier orders listing",
     *     description="Retrieve a paginated list of orders for the authenticated supplier with optional filtering",
     *     tags={"Supplier Orders"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="orderStatus",
     *         in="query",
     *         description="Filter by order status",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="shipped")
     *     ),
     *
     *     @OA\Parameter(
     *         name="shippingMethod",
     *         in="query",
     *         description="Filter by shipping method",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="2")
     *     ),
     *
     *     @OA\Parameter(
     *         name="startDate",
     *         in="query",
     *         description="Filter orders from this date (YYYY-MM-DD)",
     *         required=false,
     *
     *         @OA\Schema(type="string", format="date", example="2025-08-01")
     *     ),
     *
     *     @OA\Parameter(
     *         name="endDate",
     *         in="query",
     *         description="Filter orders until this date (YYYY-MM-DD)",
     *         required=false,
     *
     *         @OA\Schema(type="string", format="date", example="2025-08-10")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved supplier orders",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Orders retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(ref="#/components/schemas/SupplierOrderListing")
     *             ),
     *
     *             @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 @OA\Property(property="first", type="string", example="http://127.0.0.1:8000/api/v1/supplier/orders?page=1"),
     *                 @OA\Property(property="last", type="string", example="http://127.0.0.1:8000/api/v1/supplier/orders?page=2"),
     *                 @OA\Property(property="next", type="string", nullable=true, example="http://127.0.0.1:8000/api/v1/supplier/orders?page=2"),
     *                 @OA\Property(property="prev", type="string", nullable=true, example=null)
     *             ),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing authentication token",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated"),
     *             @OA\Property(property="status_code", type="integer", example=401)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - User is not authorized as supplier",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Access denied"),
     *             @OA\Property(property="status_code", type="integer", example=403)
     *         )
     *     )
     * )
     */
    public function index(FilterOrderRequest $request): JsonResponse
    {
        $orderFilterDto = OrderFilterDto::fromRequest($request);

        $orders = $this->orderService->getSupplierOrders(Auth::user()->id, $orderFilterDto);

        return $this->paginatedResponse($orders, SupplierOrderListingResource::collection($orders));
    }

    /**
     * Display the specified order.
     *
     * @OA\Get(
     *     path="/supplier/orders/{order}",
     *     summary="Get supplier order details",
     *     description="Retrieve detailed information about a specific order for the authenticated supplier",
     *     tags={"Supplier Orders"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         description="Order ID to retrieve",
     *         required=true,
     *
     *         @OA\Schema(type="integer", example=111)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved order details",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Order retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="order", ref="#/components/schemas/Order")
     *             ),
     *             @OA\Property(property="status_code", type="integer", example=200)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing authentication token",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated"),
     *             @OA\Property(property="status_code", type="integer", example=401)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - User is not authorized to view this order",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You are not authorized to view this order"),
     *             @OA\Property(property="status_code", type="integer", example=403)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Order not found"),
     *             @OA\Property(property="status_code", type="integer", example=404)
     *         )
     *     )
     * )
     */
    public function show(Order $order): JsonResponse
    {
        $this->authorize('viewAsSupplier', $order);

        $order->load(['user', 'supplier', 'products', 'orderDetail', 'products.product.category',
            'products.product.media'])->loadCount('products');

        return $this->successResponse(
            data: [
                'order' => OrderResource::make($order),
            ],
            statusCode: Response::HTTP_OK
        );
    }
}

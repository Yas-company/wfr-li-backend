<?php

namespace App\Http\Controllers\api\v1\Order\Buyer;

use App\Dtos\OrderFilterDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\FilterOrderRequest;
use App\Http\Resources\Order\BuyerOrderListingResource;
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
 *     name="Buyer Orders",
 *     description="API endpoints for buyer order management"
 * )
 */
class OrderController extends Controller
{
    use ApiResponse;

    /**
     * BuyerOrderController constructor.
     */
    public function __construct(protected OrderService $orderService)
    {
        //
    }

    /**
     * Display a listing of the user's orders.
     *
     * @OA\Get(
     *     path="/buyer/orders",
     *     summary="Get buyer orders list",
     *     description="Retrieve a paginated list of orders for the authenticated buyer with optional filtering",
     *     tags={"Buyer Orders"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter orders by status",
     *         required=false,
     *
     *         @OA\Schema(type="string", enum={"pending", "processing", "shipped", "delivered", "cancelled"})
     *     ),
     *
     *     @OA\Parameter(
     *         name="shipping_method",
     *         in="query",
     *         description="Filter orders by shipping method",
     *         required=false,
     *
     *         @OA\Schema(type="string", enum={"standard", "express", "overnight"})
     *     ),
     *
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="Filter orders from this date (YYYY-MM-DD)",
     *         required=false,
     *
     *         @OA\Schema(type="string", format="date")
     *     ),
     *
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="Filter orders until this date (YYYY-MM-DD)",
     *         required=false,
     *
     *         @OA\Schema(type="string", format="date")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved buyer orders",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Orders retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     ref="#/components/schemas/BuyerOrderListing"
     *                 )
     *             ),
     *
     *             @OA\Property(
     *                 property="pagination",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=50)
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
     *         description="Forbidden - User does not have buyer role",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Access denied. Buyer role required."),
     *             @OA\Property(property="status_code", type="integer", example=403)
     *         )
     *     )
     * )
     */
    public function index(FilterOrderRequest $request): JsonResponse
    {
        $orderFilterDto = OrderFilterDto::fromRequest($request);
        $orders = $this->orderService->getBuyerOrders(Auth::user()->id, $orderFilterDto);

        return $this->paginatedResponse($orders, BuyerOrderListingResource::collection($orders));
    }

    /**
     * Display the specified order.
     *
     * @OA\Get(
     *     path="/buyer/orders/{order}",
     *     summary="Get buyer order details",
     *     description="Retrieve detailed information about a specific order for the authenticated buyer",
     *     tags={"Buyer Orders"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         description="Order ID",
     *         required=true,
     *
     *         @OA\Schema(type="integer", example=1)
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
     *                 @OA\Property(
     *                     property="order",
     *                     ref="#/components/schemas/Order"
     *                 )
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
        $this->authorize('viewAsBuyer', $order);

        $order->load(['user', 'supplier', 'products', 'orderDetail', 'products.product.category',
            'products.product.media'])->loadCount('products');

        return $this->successResponse(
            data: [
                'order' => OrderResource::make($order),
            ],
            statusCode: Response::HTTP_OK
        );
    }

    /**
     * Reorder products from a previous order.
     *
     * @OA\Post(
     *     path="/buyer/orders/{order}/reorder",
     *     summary="Reorder products from an existing order",
     *     description="Add all products from a previous order to the current cart, clearing the cart first",
     *     tags={"Buyer Orders"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         description="Order ID to reorder from",
     *         required=true,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successfully reordered products",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Order reordered successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="added_count", type="integer", example=3, description="Number of products successfully added to cart"),
     *                 @OA\Property(
     *                     property="succeeded_products",
     *                     type="array",
     *
     *                     @OA\Items(
     *                         type="object",
     *
     *                         @OA\Property(property="product_id", type="integer", example=1),
     *                         @OA\Property(property="quantity", type="integer", example=2),
     *                         @OA\Property(property="name", type="string", example="Product Name")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="errors",
     *                     type="array",
     *
     *                     @OA\Items(type="string"),
     *                     example={"Product out of stock", "Product not found"},
     *                     description="Array of error messages for products that couldn't be added"
     *                 )
     *             ),
     *
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
     *         description="Forbidden - User is not authorized to reorder this order",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You are not authorized to reorder this order"),
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
    public function reorder(Order $order): JsonResponse
    {
        $this->authorize('viewAsBuyer', $order);

        $result = $this->orderService->reorder($order, Auth::user());

        return $this->successResponse(
            data: [
                'added_count' => $result['added_count'],
                'succeeded_products' => $result['succeeded_products'],
                'errors' => $result['errors'],
            ],
            message: __('messages.order_reordered'),
            statusCode: Response::HTTP_OK
        );
    }
}

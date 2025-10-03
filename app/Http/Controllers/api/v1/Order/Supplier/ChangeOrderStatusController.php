<?php

namespace App\Http\Controllers\api\v1\Order\Supplier;

use App\Enums\Order\OrderStatus;
use App\Events\OrderStatusUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\ChangeOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\Order\OrderStatusService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(
 *     name="Supplier Orders",
 *     description="Supplier order management endpoints"
 * )
 */
class ChangeOrderStatusController extends Controller
{
    use ApiResponse;

    /**
     * Change the order status.
     *
     * @OA\Post(
     *     path="/supplier/orders/{order}/change-status",
     *     summary="Change order status",
     *     description="Update the status of a specific order for the authenticated supplier",
     *     tags={"Supplier Orders"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         description="Order ID to update status",
     *         required=true,
     *
     *         @OA\Schema(type="integer", example=111)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"status"},
     *
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 description="New order status",
     *                 example="shipped",
     *                 enum={"pending", "confirmed", "processing", "shipped", "delivered", "cancelled"}
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Order status updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تحديث حالة الطلب بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="order",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=111),
     *                     @OA\Property(property="user_id", type="integer", example=669),
     *                     @OA\Property(
     *                         property="status",
     *                         type="object",
     *                         @OA\Property(property="value", type="string", example="delivered"),
     *                         @OA\Property(property="label", type="string", example="تم التوصيل")
     *                     ),
     *                     @OA\Property(property="total", type="string", example="1445.00"),
     *                     @OA\Property(property="total_discount", type="string", example="200.63"),
     *                     @OA\Property(property="order_type", type="string", example="1"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-10T16:24:46.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-10T19:51:55.000000Z"),
     *                     @OA\Property(property="deleted_at", type="string", nullable=true, example=null),
     *                     @OA\Property(property="products_count", type="integer", nullable=true, example=null)
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - Invalid status value",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid order status"),
     *             @OA\Property(property="status_code", type="integer", example=400)
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
     *         description="Forbidden - User is not authorized to update this order",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You are not authorized to update this order"),
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
    public function __invoke(ChangeOrderStatusRequest $request, Order $order, OrderStatusService $orderStatusService): JsonResponse
    {
        $this->authorize('viewAsSupplier', $order);

        $newStatus = OrderStatus::tryFrom($request->validated('status'));

        $order = $orderStatusService->changeOrderStatus($order, $newStatus);

        broadcast(new OrderStatusUpdated($order, $newStatus));

        return $this->successResponse(
            data: [
                'order' => OrderResource::make($order),
            ],
            message: __('messages.orders.order_status_updated'),
            statusCode: Response::HTTP_OK
        );
    }
}

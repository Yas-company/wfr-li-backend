<?php

namespace App\Http\Controllers\Api\V1\Order\Supplier;

use App\Models\Order;
use App\Enums\Order\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Order\ChangeOrderStatusRequest;
use App\Services\Order\OrderStatusService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ChangeOrderStatusController extends Controller
{
    use ApiResponse;

    /**
     * Change the order status.
     *
     * @param ChangeOrderStatusRequest $request
     * @param Order $order
     * @param OrderStatusService $orderStatusService
     *
     * @return JsonResponse
     */
    public function __invoke(ChangeOrderStatusRequest $request, Order $order, OrderStatusService $orderStatusService): JsonResponse
    {
        $this->authorize('viewAsSupplier', $order);

        $newStatus = OrderStatus::tryFrom($request->validated('status'));

        $order = $orderStatusService->changeOrderStatus($order, $newStatus);

        return $this->successResponse(
            data: [
                'order' => OrderResource::make($order),
            ],
            message: __('messages.orders.order_status_updated'),
            statusCode: Response::HTTP_OK
        );
    }
}

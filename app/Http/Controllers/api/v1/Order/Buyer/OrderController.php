<?php

namespace App\Http\Controllers\api\v1\Order\Buyer;

use App\Dtos\OrderFilterDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\FilterOrderRequest;
use App\Http\Resources\Order\BuyerOrderListingResource;
use App\Http\Resources\Order\OrderResource;
use App\Models\Order;
use App\Services\Order\OrderService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

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
     */
    public function index(FilterOrderRequest $request): JsonResponse
    {
        $orderFilterDto = OrderFilterDto::fromRequest($request);
        $orders = $this->orderService->getBuyerOrders(Auth::user()->id, $orderFilterDto);

        return $this->paginatedResponse($orders, BuyerOrderListingResource::collection($orders));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): JsonResponse
    {
        $this->authorize('viewAsBuyer', $order);

        $order->load(['user', 'supplier', 'products', 'orderDetail'])->loadCount('products');

        return $this->successResponse(
            data: [
                'order' => OrderResource::make($order),
            ],
            statusCode: Response::HTTP_OK
        );
    }

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

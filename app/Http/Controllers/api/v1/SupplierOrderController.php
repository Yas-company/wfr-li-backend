<?php

namespace App\Http\Controllers\api\v1;

use App\Models\Order;
use App\Traits\ApiResponse;
use App\Dtos\OrderFilterDto;
use App\Services\OrderService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OrderResource;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Order\FilterOrderRequest;
use App\Http\Resources\Order\SupplierOrderListingResource;
use Illuminate\Http\JsonResponse;

class SupplierOrderController extends Controller
{
    use ApiResponse;

    /**
     * SupplierOrderController constructor.
     *
     * @param OrderService $orderService
     */
    public function __construct(protected OrderService $orderService)
    {
        //
    }

    /**
     * Display a listing of the user's orders.
     *
     * @param FilterOrderRequest $request
     *
     * @return JsonResponse
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
     * @param Order $order
     *
     * @return JsonResponse
     */
    public function show(Order $order): JsonResponse
    {
        $this->authorize('viewAsSupplier', $order);

        $order->load(['user', 'supplier', 'products', 'orderDetail'])->loadCount('products');

        return $this->successResponse(
            data: [
                'order' => OrderResource::make($order),
            ],
            statusCode: Response::HTTP_OK
        );
    }
}

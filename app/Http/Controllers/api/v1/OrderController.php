<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Http\Requests\UploadProofRequest;
use App\Http\Resources\OrderResource;
use App\Http\Services\Contracts\OrderServiceInterface;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    use ApiResponse;

    protected OrderServiceInterface $orderService;

    public function __construct(OrderServiceInterface $orderService)
    {
        $this->orderService = $orderService;
    }

    public function checkout(CheckoutRequest $request)
    {
        try {
            $order = $this->orderService->checkout($request->validated());

            return $this->successResponse(
                new OrderResource($order),
                'تم إنشاء الطلب بنجاح'
            );
        } catch (Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }

    public function myOrders()
    {
        $orders = auth()->user()->orders()->with(['items', 'paymentMethod'])->latest()->paginate(10);

        return $this->paginatedResponse($orders,
            OrderResource::collection($orders),
            'قائمة الطلبات'
        );
    }

    public function show($id)
    {
        $order = auth()->user()->orders()
            ->with(['items.product', 'paymentMethod'])
            ->findOrFail($id);

        return $this->successResponse(
            new OrderResource($order),
            'تفاصيل الطلب'
        );
    }

    public function uploadPaymentProof(UploadProofRequest $request)
    {
        $order = auth()->user()->orders()->where('id', $request->order_id)->firstOrFail();

        if ($order->payment_proof) {
            Storage::disk('public')->delete($order->payment_proof);
        }

        $path = $request->file('payment_proof')->store('payment_proofs', 'public');
        $order->update(['payment_proof' => $path]);

        return $this->successResponse(null, 'تم رفع الفاتورة بنجاح');
    }
}

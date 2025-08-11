<?php

namespace App\Http\Controllers\api\v1;

use App\Models\Order;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Enums\Order\OrderStatus;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Services\Payment\PaymentService;

class PaymentController extends Controller
{
    use ApiResponse;

    public function pay(Order $order, PaymentService $paymentService)
    {
        return redirect($paymentService->redirect($order));
    }

    public function callback(Request $request, PaymentService $paymentService)
    {
        $result = $paymentService->handleCallback($request);

        $order = Order::findOrFail($result['order_id']);

        DB::transaction(function () use ($order, $result) {
            match ($result['status']) {
                'paid' => $this->handleSuccessfulPayment($order),
                'failed' => $this->handleFailedPayment($order),
                default => null
            };
        });

        return redirect()->route('order.status', $order->id);
    }

    protected function handleSuccessfulPayment(Order $order)
    {
        $order->update(['status' => OrderStatus::PAID]);

        foreach ($order->products as $product) {
            $product->product->decrement('stock_qty', $product->quantity);
        }

        //event(new OrderPaid($order));
    }

    protected function handleFailedPayment(Order $order)
    {
        $order->update(['status' => OrderStatus::FAILED]);
    }
}

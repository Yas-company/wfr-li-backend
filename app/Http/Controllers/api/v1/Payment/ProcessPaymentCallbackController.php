<?php

namespace App\Http\Controllers\api\v1\Payment;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentService;
use Illuminate\Support\Facades\Log;

class ProcessPaymentCallbackController extends Controller
{
    use ApiResponse;

    public function __invoke(Request $request, PaymentService $paymentService)
    {
        Log::channel('payments')->info('payment.callback.controller.start', [
            'gateway' => 'tap',
            'method' => $request->method(),
            'tap_id' => $request->input('tap_id'),
            'ip' => $request->ip(),
        ]);

        $response = $paymentService->callback($request);

        Log::channel('payments')->info('payment.callback.controller.result', [
            'gateway' => 'tap',
            'tap_id' => $request->input('tap_id'),
            'success' => $response['success'] ?? null,
            'message' => $response['message'] ?? null,
        ]);

        if($response['success'] === true) {
            return redirect()->route('payment.success');
        }

        return redirect()->route('payment.fail');
    }
}

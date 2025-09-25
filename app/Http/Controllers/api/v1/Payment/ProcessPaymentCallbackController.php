<?php

namespace App\Http\Controllers\api\v1\Payment;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentService;

class ProcessPaymentCallbackController extends Controller
{
    use ApiResponse;

    public function __invoke(Request $request, PaymentService $paymentService)
    {
        $response = $paymentService->callback($request);

        if($response['success'] === true) {
            return redirect()->route('payment.success');
        }

        return redirect()->route('payment.fail');
    }
}

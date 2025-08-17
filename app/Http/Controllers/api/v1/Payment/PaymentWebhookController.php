<?php

namespace App\Http\Controllers\api\v1\Payment;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentService;

class PaymentWebhookController extends Controller
{
    use ApiResponse;

    public function __invoke(Request $request, PaymentService $paymentService)
    {
        $paymentService->webhook($request);
    }
}

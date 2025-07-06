<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Services\Contracts\PaymentServiceInterface;
use App\Http\Services\Payment\PaymentContext;
use App\Http\Services\Payment\PaymentFactory;
use App\Http\Services\Payment\PaymentService;
use App\Http\Services\Payment\Strategies\TapPaymentStrategy;
use App\Models\Payment;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use ApiResponse;

    protected $paymentService;
    public function __construct(PaymentServiceInterface $paymentService)
    {
        $this->paymentService = $paymentService;
    }


    public function create(Request $request)
    {
        $method = $request->input('method');
        $strategy = PaymentFactory::make($method);

        $context = new PaymentContext();
        $context->setStrategy($strategy);

        //$result = $context->createPayment($request->all());

        //return $this->successResponse($result, 'تم إنشاء الدفع بنجاح');
    }


    public function callback(Request $request)
    {
        $context = new PaymentContext();
        $context->setStrategy(new TapPaymentStrategy());

        $result = $context->verifyPayment($request->tap_id);

        $this->paymentService->storePayment($request,$result);

        return $this->successResponse($result, 'تم تحديث حالة الدفع');
    }
}

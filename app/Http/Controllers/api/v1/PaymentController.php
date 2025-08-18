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
use OpenApi\Annotations as OA;

class PaymentController extends Controller
{
    use ApiResponse;

    protected $paymentService;
    public function __construct(PaymentServiceInterface $paymentService)
    {
        $this->paymentService = $paymentService;
    }


    /**
     * Create a payment intent/session
     *
     * @OA\Post(
     *     path="/payment/create",
     *     summary="Create payment",
     *     description="Create a payment using the selected method",
     *     security={{"bearerAuth":{}}},
     *     tags={"Payment"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"amount","description","customer_name","email","phone","country_code","method"},
     *             @OA\Property(property="amount", type="number", format="float", example=150),
     *             @OA\Property(property="description", type="string", example="دفع قيمة أوردر"),
     *             @OA\Property(property="customer_name", type="string", example="Ahmed Ali"),
     *             @OA\Property(property="email", type="string", format="email", example="ahmed@example.com"),
     *             @OA\Property(property="phone", type="string", example="500000000"),
     *             @OA\Property(property="country_code", type="string", example="966"),
     *             @OA\Property(property="method", type="integer", enum={1,2}, example=1, description="1: CASH_ON_DELIVERY, 2: Tap")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment created",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(property="data", type="object", example={"id":"pi_123","status":"created"})
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Unsupported payment method or provider error",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unsupported payment method"),
     *             @OA\Property(property="errors", nullable=true)
     *         )
     *     )
     * )
     */
    public function create(Request $request)
    {
        $method = $request->input('method');
        $strategy = PaymentFactory::make($method);

        $context = new PaymentContext();
        $context->setStrategy($strategy);

        //$result = $context->createPayment($request->all());

        //return $this->successResponse($result, 'تم إنشاء الدفع بنجاح');
    }


    /**
     * Payment provider callback
     *
     * @OA\Get(
     *     path="/payment/callback",
     *     summary="Payment callback",
     *     description="Handle payment provider callback to verify payment",
     *     security={{"bearerAuth":{}}},
     *     tags={"Payment"},
     *     @OA\Parameter(
     *         name="tap_id",
     *         in="query",
     *         required=true,
     *         description="Tap payment ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment verified",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Payment status updated"),
     *             @OA\Property(property="data", type="object", example={"status":"CAPTURED"})
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(
     *         response=422,
     *         description="Missing or invalid tap_id",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function callback(Request $request)
    {
        $context = new PaymentContext();
        $context->setStrategy(new TapPaymentStrategy());

        $result = $context->verifyPayment($request->tap_id);

        $this->paymentService->storePayment($request,$result);

        return $this->successResponse($result, __('messages.payment.status_updated'));
    }
}

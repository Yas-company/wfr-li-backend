<?php

namespace App\Services\Payment;

use Exception;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use App\Services\Contracts\PaymentGatewayInterface;

class PaymentService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected array $headers;

    public function __construct( protected PaymentGatewayInterface $gateway)
    {
        //
    }

    /**
     * Perform request to the payment gateway.
     *
     * @param string $method
     * @param string $url
     * @param array $data
     * @param string $type
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function performRequest(string $method, string $url, array $data = [], string $type = 'json'): JsonResponse
    {
        try {
            $response = Http::withHeaders($this->headers)->send($method, $url, [
                $type => $data,
            ]);

            return response()->json([
                'success' => $response->successful(),
                'status' => $response->status(),
                'data' => $response->json(),
            ], $response->status());

        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Initiate payment.
     *
     * @param Order $order
     *
     * @return array
     */
    public function initiatePayment(Order $order): array
    {
        return $this->gateway->initiatePayment($order);
    }

    /**
     * Handle callback/webhook and return payment result.
     *
     * @param Request $request
     *
     * @return array
     */
    public function webhook(Request $request): array
    {
        return $this->gateway->webhook($request);
    }
}

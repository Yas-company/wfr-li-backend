<?php

namespace App\Services\Payment;

use App\Enums\Payment\Tap\TapPaymentSource;
use App\Enums\Payment\Tap\TapPaymentStatus;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\Contracts\PaymentGatewayInterface;

class TapPaymentService extends PaymentService implements PaymentGatewayInterface
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.payment.tap.base_url') . '/v2/charges/';
        $this->apiKey = config('services.payment.tap.api_key');
        $this->headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer " . $this->apiKey,
        ];
    }

    /**
     * initiate payment
     *
     * @param Order $order
     *
     * @return array
     */
    public function initiatePayment(Order $order): array
    {
        $data = [];

        $data['amount'] = $order->total;
        $data['currency'] = config('app.currency');
        $data['threeDSecure'] = true;
        $data['customer']['first_name'] = $order->user->name;
        $data['customer']['email'] = $order->user->email;
        $data['customer']['phone'] = $order->user->phone;
        $data['source']['id'] = TapPaymentSource::ALL->value;
        $data['post']['url'] = route('payment.webhook');
        $data['redirect']['url'] = route('payment.webhook');
        $data['reference']['order'] = $order->id;

        $response = $this->performRequest('POST', $this->baseUrl, $data);

        $responseData = $response->getData(true);

        if ($responseData['success']) {
            return  [
                'success' => true,
                'url' => $responseData['data']['transaction']['url'],
            ];
        }

        throw new \Exception('Failed to initiate Tap payment');
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
        $chargeId = $request->input('tap_id');
        $response = $this->retrievePayment($chargeId);
        dd($response);
        if($response['success'] && $response['data']['status'] === TapPaymentStatus::CAPTURED->value)
        {
            $order = Order::find($response['data']['reference']['order']);
            dd($order);
        }

        return [];
        /*
        return [
            'order_id' => (int) str_replace('order_', '', $payload['reference']['transaction']),
            'status' => match ($payload['status']) {
                'CAPTURED' => 'paid',
                'FAILED' => 'failed',
                default => 'pending'
            }
        ];
        */
    }

    /**
     * Redirect user to payment page or return payment URL.
     *
     * @param Request $request
     *
     * @return string
     */
    public function redirect(Request $request): string
    {
        $chargeId = $request->input('tap_id');
        $response = $this->retrievePayment($chargeId);
        $orderId = $response['data']['reference']['order'];

        if($response['success'] && $response['data']['status'] === TapPaymentStatus::CAPTURED->value)
        {
            return "https://wfrli.com/payment/success?order_id={$orderId}";
        } else {
            return "https://wfrli.com/payment/failed?order_id={$orderId}";
        }

    }

    /**
     * Retrieve payment details.
     *
     * @param string $chargeId
     *
     * @return array
     */
    protected function retrievePayment(string $chargeId): array
    {
        $response = $this->performRequest('GET', $this->baseUrl . $chargeId);

        return $response->getData(true);
    }
}

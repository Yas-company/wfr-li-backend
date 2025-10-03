<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Events\PaymentSuccessful;
use Illuminate\Http\Request;
use App\Enums\Order\OrderStatus;
use App\Enums\Payment\Tap\TapPaymentSource;
use App\Enums\Payment\Tap\TapPaymentStatus;
use App\Services\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $data['redirect']['url'] = route('payment.callback');
        $data['reference']['order'] = $order->id;
        $data['reference']['idempotent'] = 'txn_' . $order->id;
        $data['transaction']['expiry']['period'] = 10;
        $data['transaction']['expiry']['type'] = 'MINUTE';

        Log::channel('payments')->info('payment.initiate.request', [
            'gateway' => 'tap',
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'amount' => $data['amount'],
            'currency' => $data['currency'],
        ]);

        $response = $this->performRequest('POST', $this->baseUrl, $data);

        $responseData = $response->getData(true);

        if ($responseData['success']) {
            Log::channel('payments')->info('payment.initiate.response', [
                'gateway' => 'tap',
                'order_id' => $order->id,
                'status_code' => $responseData['status'] ?? null,
                'success' => true,
            ]);
            return  [
                'success' => true,
                'url' => $responseData['data']['transaction']['url'],
            ];
        }
        Log::channel('payments')->error('payment.initiate.response', [
            'gateway' => 'tap',
            'order_id' => $order->id,
            'status_code' => $responseData['status'] ?? null,
            'success' => false,
            'error' => $responseData['message'] ?? 'unknown_error',
        ]);
        throw new \Exception('Failed to initiate Tap payment');
    }

    /**
     * Handle callback/webhook and return payment result.
     *
     * @param Request $request
     *
     * @return array
     */
    public function callback(Request $request): array
    {
        $chargeId = $request->input('tap_id');

        Log::channel('payments')->info('payment.callback.received', [
            'gateway' => 'tap',
            'tap_id' => $chargeId,
            'method' => $request->method(),
            'ip' => $request->ip(),
        ]);

        $response = $this->retrievePayment($chargeId);

        $orderId = $response['data']['reference']['order'] ?? null;
        $providerStatus = $response['data']['status'] ?? null;
        $providerAmount = $response['data']['amount'] ?? null;
        $providerCurrency = $response['data']['currency'] ?? null;

        Log::channel('payments')->info('payment.tap.retrieve', [
            'gateway' => 'tap',
            'tap_id' => $chargeId,
            'order_id' => $orderId,
            'provider_status' => $providerStatus,
            'amount' => $providerAmount,
            'currency' => $providerCurrency,
        ]);

        $order = Order::where('status', OrderStatus::PENDING_PAYMENT)->find($orderId);

        if(!$this->validateCharge($providerAmount, $providerCurrency, $chargeId, $order)) {
            return ['success'=>false, 'message'=>'Charge validation failed'];
        }

        if (!$order) {
            Log::channel('payments')->warning('payment.callback.order_not_found', [
                'gateway' => 'tap',
                'tap_id' => $chargeId,
                'order_id' => $orderId,
            ]);
            return ['success'=>false, 'message'=>'Order not found'];
        }

        if($response['success'] && $providerStatus === TapPaymentStatus::CAPTURED->value)
        {
            try{
                DB::beginTransaction();

                $order->update([
                    'status' => OrderStatus::PAID,
                    'charge_id' => $chargeId,
                ]);

                PaymentSuccessful::dispatch($order, $order->user);
                $order->reservedStock()->delete();

                DB::commit();

                Log::channel('payments')->info('payment.callback.fulfilled', [
                    'gateway' => 'tap',
                    'tap_id' => $chargeId,
                    'order_id' => $order->id,
                    'new_status' => $order->status->value,
                ]);
                return [
                    'success' => true,
                    'message' => 'Payment success',
                ];
            } catch(\Exception $e) {
                DB::rollBack();
                $order->update([
                    'status' => OrderStatus::FAILED,
                ]);
                Log::channel('payments')->error('payment.callback.error', [
                    'gateway' => 'tap',
                    'tap_id' => $chargeId,
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
                return [
                    'success' => false,
                    'message' => $e->getMessage(),
                ];
            }
        }

        $order->update([
            'status' => OrderStatus::FAILED,
        ]);

        Log::channel('payments')->warning('payment.callback.failed', [
            'gateway' => 'tap',
            'tap_id' => $chargeId,
            'order_id' => $order->id,
            'provider_status' => $providerStatus,
        ]);

        return [
            'success' => false,
            'message' => 'Payment failed',
        ];
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

    protected function validateCharge(float $amount, string $currency, string $chargeId, Order $order)
    {
        $amount = (float) $amount;
        $orderTotal = (float) $order->total;

        if($amount !== $orderTotal) {
            Log::channel('payments')->error('payment.callback.charge_amount_mismatch', [
                'gateway' => 'tap',
                'charge_id' => $chargeId,
                'order_id' => $order->id,
                'charge_amount' => $amount,
                'order_amount' => $orderTotal,
            ]);
            return false;
        }

        if($currency !== $order->currency) {
            Log::channel('payments')->error('payment.callback.charge_currency_mismatch', [
                'gateway' => 'tap',
                'charge_id' => $chargeId,
                'order_id' => $order->id,
                'charge_currency' => $currency,
                'order_currency' => $order->currency,
            ]);
            return false;
        }

        return true;
    }
}

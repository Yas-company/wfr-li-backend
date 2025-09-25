<?php

namespace App\Services\Payment;

use App\Models\Order;
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

        $order = Order::find($orderId);

        if (!$order) {
            Log::channel('payments')->warning('payment.callback.order_not_found', [
                'gateway' => 'tap',
                'tap_id' => $chargeId,
                'order_id' => $orderId,
            ]);
            return ['success'=>false, 'message'=>'Order not found'];
        }

        if($order->status !== OrderStatus::PENDING_PAYMENT) {
            Log::channel('payments')->info('payment.callback.order_already_processed', [
                'gateway' => 'tap',
                'tap_id' => $chargeId,
                'order_id' => $order->id,
                'current_status' => $order->status->value,
            ]);
            return ['success'=>false, 'message'=>'Order not pending payment'];
        }

        if($response['success'] && $providerStatus === TapPaymentStatus::CAPTURED->value)
        {
            try{
                DB::beginTransaction();

                $order->update([
                    'status' => OrderStatus::PAID,
                ]);

                $items = $order->products;

                foreach($items as $item) {

                    $product = DB::table('products')->lockForUpdate()->find($item->product_id);

                    if($product->stock_qty < $item->quantity) {
                        throw new \Exception('Product out of stock');
                    }

                    DB::table('products')->where('id', $item->product_id)->update([
                        'stock_qty' => $product->stock_qty - $item->quantity,
                    ]);
                }

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
                Log::channel('payments')->error('payment.callback.error', [
                    'gateway' => 'tap',
                    'tap_id' => $chargeId,
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
                throw $e;
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
}

<?php

namespace App\Services\Payment;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\Contracts\PaymentGatewayInterface;

class TapPaymentService implements PaymentGatewayInterface
{
    protected string $endpoint = 'https://api.tap.company/v2';
    protected string $token;

    public function __construct()
    {
        $this->token = config('services.tap.secret_key');
    }

    public function redirect(Order $order): string
    {
        $response = Http::withToken($this->token)
            ->post("{$this->endpoint}/charges", [
                'amount' => $order->total,
                'currency' => 'SAR',
                'customer' => [
                    'email' => $order->user->email,
                    'phone' => $order->user->phone
                ],
                'redirect' => [
                    'url' => route('payment.callback', ['gateway' => 'tap']),
                ],
                'reference' => [
                    'transaction' => "order_{$order->id}",
                ],
            ]);

        if ($response->successful()) {
            return $response->json('transaction.url');
        }

        throw new \Exception('Failed to initiate Tap payment');
    }

    public function handleCallback(Request $request): array
    {
        $payload = $request->json()->all();

        return [
            'order_id' => (int) str_replace('order_', '', $payload['reference']['transaction']),
            'status' => match ($payload['status']) {
                'CAPTURED' => 'paid',
                'FAILED' => 'failed',
                default => 'pending'
            }
        ];
    }
}

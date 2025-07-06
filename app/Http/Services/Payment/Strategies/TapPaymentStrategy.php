<?php

namespace App\Http\Services\Payment\Strategies;

use App\Http\Services\Contracts\PaymentStrategyInterface;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;

class TapPaymentStrategy implements PaymentStrategyInterface
{
    protected string $secretKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('services.tap.secret_key');
        $this->baseUrl = 'https://api.tap.company/v2/';
    }

    public function createPayment(array $data,$totals_discount) :int
    {
        $response = Http::withToken($this->secretKey)
            ->post($this->baseUrl . 'charges', [
                'amount' => $data['amount'],
                'currency' => 'SAR',
                'threeDSecure' => true,
                'save_card' => false,
                'description' => $data['description'],
                'statement_descriptor' => 'wfr-li',
                'metadata' => $data['metadata'] ?? [],
                'reference' => [
                    'transaction' => uniqid('txn_'),
                ],
                'receipt' => ['email' => false, 'sms' => true],
                'customer' => [
                    'first_name' => $data['customer_name'],
                    'email' => $data['email'],
                    'phone' => [
                        'country_code' => $data['country_code'],
                        'number' => $data['phone']
                    ]
                ],
                'source' => [
                    'id' => $data['source'], // tap, mada, etc
                ],
                'redirect' => [
                    'url' => route('payment.callback') // Define this route
                ]
            ]);

        return $response->json();
    }

    public function verifyPayment(string $tap_id): array
    {
        $response = Http::withToken($this->secretKey)
            ->get($this->baseUrl . 'charges/' . $tap_id);

        return $response->json();
    }

}

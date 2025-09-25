<?php

namespace Tests\Feature\Payment;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Enums\Order\OrderStatus;
use App\Services\Payment\PaymentService;

class TapPaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.payment.default_payment_gateway' => 'tap',
            'services.payment.tap.base_url' => 'https://tap.example',
            'services.payment.tap.api_key' => 'test_api_key',
            'app.currency' => 'SAR',
        ]);
    }

    public function test_initiate_payment_returns_redirect_url_and_sends_expected_payload(): void
    {
        $buyer = User::factory()->create();
        $supplier = User::factory()->create();

        $order = Order::factory()->for($buyer, 'user')->create([
            'supplier_id' => $supplier->id,
            'status' => OrderStatus::PENDING_PAYMENT,
            'total' => 150.00,
        ]);

        Http::fake([
            'https://tap.example/v2/charges/' => Http::response([
                'transaction' => [
                    'url' => 'https://pay.example/txn_123',
                ],
            ], 200),
        ]);

        $paymentService = app(PaymentService::class);
        $result = $paymentService->initiatePayment($order);

        $this->assertTrue($result['success']);
        $this->assertSame('https://pay.example/txn_123', $result['url']);

        Http::assertSent(function ($request) use ($order) {
            $json = $request->data();
            return $request->method() === 'POST'
                && str_contains((string) $request->url(), '/v2/charges/')
                && $json['amount'] == $order->total
                && $json['currency'] === config('app.currency')
                && ($json['reference']['order'] ?? null) === $order->id
                && isset($json['redirect']['url']);
        });
    }

    public function test_callback_captured_marks_order_paid_and_deducts_stock(): void
    {
        [$order, $product] = $this->makeOrderWithProduct(quantity: 3, stock: 10, total: 300.00);

        Http::fake([
            'https://tap.example/v2/charges/ch_123' => Http::response([
                'status' => 'CAPTURED',
                'reference' => [
                    'order' => $order->id,
                ],
                'amount' => 300.00,
                'currency' => 'SAR',
            ], 200),
        ]);

        $request = Request::create('/api/v1/payment/callback', 'GET', ['tap_id' => 'ch_123']);

        $result = app(PaymentService::class)->callback($request);

        $this->assertTrue($result['success']);
        $this->assertSame('Payment success', $result['message']);

        $this->assertSame(OrderStatus::PAID, $order->fresh()->status);
        $this->assertSame(7, $product->fresh()->stock_qty); // 10 - 3
    }

    public function test_callback_failed_marks_order_failed_and_keeps_stock(): void
    {
        [$order, $product] = $this->makeOrderWithProduct(quantity: 2, stock: 5, total: 100.00);

        Http::fake([
            'https://tap.example/v2/charges/ch_123' => Http::response([
                'status' => 'FAILED',
                'reference' => [
                    'order' => $order->id,
                ],
                'amount' => 100.00,
                'currency' => 'SAR',
            ], 200),
        ]);

        $request = Request::create('/api/v1/payment/callback', 'GET', ['tap_id' => 'ch_123']);

        $result = app(PaymentService::class)->callback($request);

        $this->assertFalse($result['success']);
        $this->assertSame('Payment failed', $result['message']);

        $this->assertSame(OrderStatus::FAILED, $order->fresh()->status);
        $this->assertSame(5, $product->fresh()->stock_qty); // unchanged
    }

    public function test_callback_is_idempotent_when_order_already_paid(): void
    {
        [$order, $product] = $this->makeOrderWithProduct(quantity: 1, stock: 4, total: 50.00);
        $order->update(['status' => OrderStatus::PAID]);

        Http::fake([
            'https://tap.example/v2/charges/ch_123' => Http::response([
                'status' => 'CAPTURED',
                'reference' => [
                    'order' => $order->id,
                ],
                'amount' => 50.00,
                'currency' => 'SAR',
            ], 200),
        ]);

        $request = Request::create('/api/v1/payment/callback', 'GET', ['tap_id' => 'ch_123']);

        $result = app(PaymentService::class)->callback($request);

        $this->assertFalse($result['success']);
        $this->assertSame('Order not pending payment', $result['message']);
        $this->assertSame(4, $product->fresh()->stock_qty); // unchanged
    }

    public function test_callback_controller_redirects_on_success_and_failure(): void
    {
        [$order] = $this->makeOrderWithProduct(quantity: 1, stock: 2, total: 20.00);

        // success
        Http::fake([
            'https://tap.example/v2/charges/ch_ok' => Http::response([
                'status' => 'CAPTURED',
                'reference' => [
                    'order' => $order->id,
                ],
            ], 200),
        ]);
        $this->get(route('payment.callback', ['tap_id' => 'ch_ok']))
            ->assertRedirect(route('payment.success'));

        // failure
        Http::fake([
            'https://tap.example/v2/charges/ch_bad' => Http::response([
                'status' => 'FAILED',
                'reference' => [
                    'order' => $order->id,
                ],
            ], 200),
        ]);
        $this->get(route('payment.callback', ['tap_id' => 'ch_bad']))
            ->assertRedirect(route('payment.fail'));
    }

    /**
     * Helper to create an order with a single product line and desired stock/qty.
     *
     * @return array{0: Order, 1: Product}
     */
    private function makeOrderWithProduct(int $quantity, int $stock, float $total): array
    {
        $buyer = User::factory()->create();
        $supplier = User::factory()->create();

        $product = Product::factory()->create([
            'supplier_id' => $supplier->id,
            'stock_qty' => $stock,
            'price' => $total / max($quantity, 1),
        ]);

        $order = Order::factory()->for($buyer, 'user')->create([
            'supplier_id' => $supplier->id,
            'status' => OrderStatus::PENDING_PAYMENT,
            'total' => $total,
        ]);

        OrderProduct::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price' => $product->price,
        ]);

        return [$order->fresh('products'), $product];
    }
}



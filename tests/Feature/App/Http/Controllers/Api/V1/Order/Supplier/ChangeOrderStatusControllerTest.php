<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1\Order\Supplier;

use App\Enums\Order\OrderStatus;
use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChangeOrderStatusControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $supplier;
    protected User $otherSupplier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->supplier = $this->createSupplier();
        $this->otherSupplier = $this->createSupplier();

        $this->withHeader('Accept-Language', 'en');
    }

    public function test_supplier_can_change_order_status_from_accepted_to_shipped()
    {
        $order = $this->createOrder(OrderStatus::ACCEPTED, $this->supplier->id);

        $response = $this->actingAs($this->supplier)
            ->postJson(route('supplier.orders.change-status', $order), [
                'status' => OrderStatus::SHIPPED,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'order' => [
                        'status',
                    ],
                ],
            ]);

        $data = $response->json('data.order');

        $this->assertEquals(OrderStatus::SHIPPED->value, $data['status']);
    }

    public function test_supplier_can_change_order_status_from_shipped_to_delivered()
    {
        $order = $this->createOrder(OrderStatus::SHIPPED, $this->supplier->id);

        $response = $this->actingAs($this->supplier)
            ->postJson(route('supplier.orders.change-status', $order), [
                'status' => OrderStatus::DELIVERED,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'order' => [
                        'status',
                    ],
                ],
            ]);

        $data = $response->json('data.order');

        $this->assertEquals(OrderStatus::DELIVERED->value, $data['status']);
    }

    public function test_supplier_cannot_change_order_status_from_pending_to_shipped()
    {
        $order = $this->createOrder(OrderStatus::PENDING, $this->supplier->id);

        $response = $this->actingAs($this->supplier)
            ->postJson(route('supplier.orders.change-status', $order), [
                'status' => OrderStatus::SHIPPED,
            ]);

        $response->assertStatus(422);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::PENDING,
        ]);
    }

    public function test_supplier_cannot_change_order_status_from_pending_to_delivered()
    {
        $order = $this->createOrder(OrderStatus::PENDING, $this->supplier->id);

        $response = $this->actingAs($this->supplier)
            ->postJson(route('supplier.orders.change-status', $order), [
                'status' => OrderStatus::DELIVERED,
            ]);

        $response->assertStatus(422);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::PENDING,
        ]);
    }

    public function test_supplier_cannot_change_order_status_from_rejected_to_shipped()
    {
        $order = $this->createOrder(OrderStatus::REJECTED, $this->supplier->id);

        $response = $this->actingAs($this->supplier)
            ->postJson(route('supplier.orders.change-status', $order), [
                'status' => OrderStatus::SHIPPED,
            ]);

        $response->assertStatus(422);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::REJECTED,
        ]);
    }

    public function test_supplier_cannot_change_order_status_from_rejected_to_delivered()
    {
        $order = $this->createOrder(OrderStatus::REJECTED, $this->supplier->id);

        $response = $this->actingAs($this->supplier)
            ->postJson(route('supplier.orders.change-status', $order), [
                'status' => OrderStatus::DELIVERED,
            ]);

        $response->assertStatus(422);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::REJECTED,
        ]);
    }

    public function test_supplier_cannot_change_order_status_from_paid_to_shipped()
    {
        $order = $this->createOrder(OrderStatus::PAID, $this->supplier->id);

        $response = $this->actingAs($this->supplier)
            ->postJson(route('supplier.orders.change-status', $order), [
                'status' => OrderStatus::SHIPPED,
            ]);

        $response->assertStatus(422);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::PAID,
        ]);
    }

    public function test_supplier_cannot_change_order_status_from_paid_to_delivered()
    {
        $order = $this->createOrder(OrderStatus::PAID, $this->supplier->id);

        $response = $this->actingAs($this->supplier)
            ->postJson(route('supplier.orders.change-status', $order), [
                'status' => OrderStatus::DELIVERED,
            ]);

        $response->assertStatus(422);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::PAID,
        ]);
    }

    public function test_supplier_cannot_change_other_supplier_order_status()
    {
        $order = $this->createOrder(OrderStatus::ACCEPTED, $this->otherSupplier->id);

        $response = $this->actingAs($this->supplier)
            ->postJson(route('supplier.orders.change-status', $order), [
                'status' => OrderStatus::SHIPPED,
            ]);

        $response->assertStatus(404);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::ACCEPTED,
        ]);
    }

    // helpers
    protected function createSupplier(array $attributes = []): User
    {
        $user = User::factory()->create(array_merge([
            'role' => UserRole::SUPPLIER,
            'status' => UserStatus::APPROVED,
            'is_verified' => true,
        ], $attributes));

        return $user;
    }

    protected function createOrder(OrderStatus $status, int $supplierId)
    {
        return Order::factory()->create([
            'supplier_id' => $supplierId,
            'status' => $status,
        ]);
    }
}

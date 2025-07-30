<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1\Order\Supplier;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Enums\UserRole;
use App\Models\Address;
use App\Enums\UserStatus;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $supplier;
    protected User $otherSupplier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->supplier = $this->createUser(UserRole::SUPPLIER);
        $this->otherSupplier = $this->createUser(UserRole::SUPPLIER);
    }

    public function test_buyers_cannot_access_supplier_orders()
    {
        $buyer = $this->createUser(UserRole::BUYER);

        $response = $this->actingAs($buyer)->getJson(route('supplier.orders.index'));

        $response->assertStatus(401);
    }

    public function test_supplier_can_get_their_orders()
    {
        $this->createOrders($this->supplier->id, 5);

        $response = $this->actingAs($this->supplier)->getJson(route('supplier.orders.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'status',
                        'created_at',
                        'buyer_name',
                        'products_count',
                        'tracking_number',
                        'shipping_method',
                        'payment_status',
                    ],
                ],
                'links' => [
                    'first',
                    'last',
                    'next',
                    'prev',
                ]
            ]);

        $response->assertJsonCount(5, 'data');
    }

    public function test_supplier_cannot_get_other_suppliers_orders()
    {
        $this->createOrders($this->supplier->id, 5);
        $this->createOrders($this->otherSupplier->id, 5);

        $response = $this->actingAs($this->supplier)->getJson(route('supplier.orders.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'status',
                        'created_at',
                        'buyer_name',
                        'products_count',
                        'tracking_number',
                        'shipping_method',
                        'payment_status',
                    ],
                ],
                'links' => [
                    'first',
                    'last',
                    'next',
                    'prev',
                ]
            ]);

        $response->assertJsonCount(5, 'data');
    }

    public function test_supplier_get_his_orders_in_descending_order_by_date()
    {
        $firstOrder = Order::factory()->create([
            'supplier_id' => $this->supplier->id,
            'created_at' => now()->subDays(1),
        ]);

        $thirdOrder = Order::factory()->create([
            'supplier_id' => $this->supplier->id,
            'created_at' => now()->subDays(3),
        ]);

        $secondOrder = Order::factory()->create([
            'supplier_id' => $this->supplier->id,
            'created_at' => now()->subDays(2),
        ]);

        $response = $this->actingAs($this->supplier)->getJson(route('supplier.orders.index'));

        $orders = $response->json('data');

        $this->assertEquals($firstOrder['id'], $orders[0]['id']);
        $this->assertEquals($secondOrder['id'], $orders[1]['id']);
        $this->assertEquals($thirdOrder['id'], $orders[2]['id']);
    }

    // helpers
    protected function createUser(UserRole $role, array $attributes = []): User
    {
        $user = User::factory()->create(array_merge([
            'role' => $role,
            'status' => UserStatus::APPROVED,
            'is_verified' => true,
        ], $attributes));

        // Create associated address
        Address::factory()->create(['user_id' => $user->id]);

        return $user;
    }

    protected function createOrders(int $supplierId, int $count = 5)
    {
        Order::factory()->count($count)->create([
            'supplier_id' => $supplierId,
        ]);
    }
}

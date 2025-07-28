<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Enums\UserRole;
use App\Models\Address;
use App\Enums\UserStatus;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BuyerOrderControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $buyer;
    protected User $otherBuyer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buyer = $this->createUser(UserRole::BUYER);
        $this->otherBuyer = $this->createUser(UserRole::BUYER);
    }

    public function test_suppliers_cannot_access_buyer_orders()
    {
        $supplier = $this->createUser(UserRole::SUPPLIER);

        $response = $this->actingAs($supplier)->getJson(route('buyer.orders.index'));

        $response->assertStatus(401);
    }

    public function test_buyer_can_get_their_orders()
    {
        $this->createOrders($this->buyer->id, 5);

        $response = $this->actingAs($this->buyer)->getJson(route('buyer.orders.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'status',
                        'created_at',
                        'supplier_name',
                        'supplier_image',
                        'products_count',
                        'total',
                        'total_discount',
                        'tracking_number',
                        'shipping_method',
                        'payment_status',
                        'ratings'
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

    public function test_buyer_cannot_get_other_buyers_orders()
    {
        $this->createOrders($this->buyer->id, 5);
        $this->createOrders($this->otherBuyer->id, 5);

        $response = $this->actingAs($this->buyer)->getJson(route('buyer.orders.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'status',
                        'created_at',
                        'supplier_name',
                        'supplier_image',
                        'products_count',
                        'total',
                        'total_discount',
                        'tracking_number',
                        'shipping_method',
                        'payment_status',
                        'ratings'
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

    public function test_buyer_get_his_orders_in_descending_order_by_date()
    {
        $firstOrder = Order::factory()->create([
            'user_id' => $this->buyer->id,
            'created_at' => now()->subDays(1),
        ]);

        $thirdOrder = Order::factory()->create([
            'user_id' => $this->buyer->id,
            'created_at' => now()->subDays(3),
        ]);

        $secondOrder = Order::factory()->create([
            'user_id' => $this->buyer->id,
            'created_at' => now()->subDays(2),
        ]);

        $response = $this->actingAs($this->buyer)->getJson(route('buyer.orders.index'));

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

    protected function createOrders(int $buyerId, int $count = 5)
    {
        Order::factory()->count($count)->create([
            'user_id' => $buyerId,
        ]);
    }
}

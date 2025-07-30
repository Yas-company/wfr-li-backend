<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Enums\UserRole;
use App\Models\Address;
use App\Models\Product;
use App\Enums\UserStatus;
use App\Enums\ProductStatus;
use App\Models\OrderProduct;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BuyerOrderControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $buyer;

    protected User $otherBuyer;

    protected User $supplier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buyer = $this->createUser(UserRole::BUYER);
        $this->otherBuyer = $this->createUser(UserRole::BUYER);
        $this->supplier = $this->createUser(UserRole::SUPPLIER);
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
                        'ratings',
                    ],
                ],
                'links' => [
                    'first',
                    'last',
                    'next',
                    'prev',
                ],
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
                        'ratings',
                    ],
                ],
                'links' => [
                    'first',
                    'last',
                    'next',
                    'prev',
                ],
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

    // Reorder API Tests
    public function test_buyer_can_reorder_successfully()
    {
        $order = $this->createOrderWithProducts($this->buyer->id, $this->supplier->id, 3);

        $response = $this->actingAs($this->buyer)
            ->postJson(route('buyer.orders.reorder', $order));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'added_count',
                    'succeeded_products',
                    'errors',
                ],
            ]);

        $data = $response->json('data');
        $this->assertEquals(3, $data['added_count']);
        $this->assertCount(3, $data['succeeded_products']);
        $this->assertEmpty($data['errors']);
    }

    public function test_supplier_cannot_reorder_buyers_order()
    {
        $order = $this->createOrderWithProducts($this->buyer->id, $this->supplier->id, 2);

        $response = $this->actingAs($this->supplier)
            ->postJson(route('buyer.orders.reorder', $order));

        $response->assertStatus(401);
    }

    public function test_reorder_with_empty_order()
    {
        $order = Order::factory()->create([
            'user_id' => $this->buyer->id,
            'supplier_id' => $this->supplier->id,
        ]);

        $response = $this->actingAs($this->buyer)
            ->postJson(route('buyer.orders.reorder', $order));

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertEquals(0, $data['added_count']);
        $this->assertEmpty($data['succeeded_products']);
        $this->assertEmpty($data['errors']);
    }

    public function test_reorder_with_unavailable_products()
    {
        // Create products with zero stock
        $products = Product::factory()->count(3)->create([
            'supplier_id' => $this->supplier->id,
            'stock_qty' => 0,
        ]);

        $order = Order::factory()->create([
            'user_id' => $this->buyer->id,
            'supplier_id' => $this->supplier->id,
        ]);

        // Add products to order
        foreach ($products as $product) {
            OrderProduct::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => 2,
                'price' => $product->price,
            ]);
        }

        $response = $this->actingAs($this->buyer)
            ->postJson(route('buyer.orders.reorder', $order));

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertEquals(0, $data['added_count']);
        $this->assertEmpty($data['succeeded_products']);
        $this->assertNotEmpty($data['errors']);
    }

    public function test_reorder_with_partial_availability()
    {
        // Create products with different stock levels
        $availableProduct = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'stock_qty' => 10,
            'status' => ProductStatus::PUBLISHED,
            'is_active' => true,
        ]);

        $unavailableProduct = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'stock_qty' => 0,
            'status' => ProductStatus::PUBLISHED,
            'is_active' => true,
        ]);

        $order = Order::factory()->create([
            'user_id' => $this->buyer->id,
            'supplier_id' => $this->supplier->id,
        ]);

        // Add both products to order
        OrderProduct::create([
            'order_id' => $order->id,
            'product_id' => $availableProduct->id,
            'quantity' => 2,
            'price' => $availableProduct->price,
        ]);

        OrderProduct::create([
            'order_id' => $order->id,
            'product_id' => $unavailableProduct->id,
            'quantity' => 1,
            'price' => $unavailableProduct->price,
        ]);

        $response = $this->actingAs($this->buyer)
            ->postJson(route('buyer.orders.reorder', $order));

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertEquals(1, $data['added_count']);
        $this->assertCount(1, $data['succeeded_products']);
        $this->assertNotEmpty($data['errors']);

        // Check that the available product was added
        $this->assertEquals($availableProduct->id, $data['succeeded_products'][0]['product_id']);
    }

    public function test_reorder_with_insufficient_stock()
    {
        $product = Product::factory()->create([
            'supplier_id' => $this->supplier->id,
            'stock_qty' => 5,
            'status' => ProductStatus::PUBLISHED,
            'is_active' => true,
        ]);

        $order = Order::factory()->create([
            'user_id' => $this->buyer->id,
            'supplier_id' => $this->supplier->id,
        ]);

        // Try to order more than available stock
        OrderProduct::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 10, // More than available stock (5)
            'price' => $product->price,
        ]);

        $response = $this->actingAs($this->buyer)
            ->postJson(route('buyer.orders.reorder', $order));

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertEquals(0, $data['added_count']);
        $this->assertEmpty($data['succeeded_products']);
        $this->assertNotEmpty($data['errors']);
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

    protected function createOrderWithProducts(int $buyerId, int $supplierId, int $productCount = 3): Order
    {
        $order = Order::factory()->create([
            'user_id' => $buyerId,
            'supplier_id' => $supplierId,
        ]);

        $products = Product::factory()->count($productCount)->create([
            'supplier_id' => $supplierId,
            'stock_qty' => 10,
            'status' => ProductStatus::PUBLISHED,
            'is_active' => true,
        ]);

        foreach ($products as $product) {
            OrderProduct::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $this->faker->numberBetween(1, 3),
                'price' => $product->price,
            ]);
        }

        return $order;
    }
}

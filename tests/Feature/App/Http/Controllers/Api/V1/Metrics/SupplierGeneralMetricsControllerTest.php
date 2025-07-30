<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1\Metrics;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Enums\UserRole;
use App\Models\Product;
use App\Enums\UserStatus;
use App\Enums\Order\OrderStatus;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SupplierGeneralMetricsControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;


    public function test_supplier_can_get_general_metrics()
    {
        $supplier = $this->createUser(UserRole::SUPPLIER);

        $response = $this->actingAs($supplier)->getJson(route('metrics.supplier.general-metrics'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'orders' => [
                        'count_pending_orders',
                        'count_delivered_orders',
                        'count_cancelled_orders',
                        'total_sales',
                    ],
                    'products' => [
                        'count_all_products',
                        'count_available_products',
                        'count_out_of_stock_products',
                        'count_nearly_out_of_stock_products',
                    ],
                ],
            ]);
    }

    public function test_supplier_get_correct_metrics()
    {
        $supplier = $this->createUser(UserRole::SUPPLIER);
        $otherSupplier = $this->createUser(UserRole::SUPPLIER);

        $this->createProducts($supplier->id, 10, 5);
        $this->createProducts($supplier->id, 3, 4);
        $this->createProducts($supplier->id, 0, 2);

        $this->createProducts($otherSupplier->id, 10, 6, 6);
        $this->createProducts($otherSupplier->id, 2, 3, 2);
        $this->createProducts($otherSupplier->id, 0, 1, 1);

        $this->createOrders($supplier->id, 50, OrderStatus::PENDING, 5);
        $this->createOrders($supplier->id, 100, OrderStatus::DELIVERED, 5);
        $this->createOrders($supplier->id, 200, OrderStatus::CANCELLED, 5);

        $this->createOrders($otherSupplier->id, 100, OrderStatus::PENDING, 6);
        $this->createOrders($otherSupplier->id, 60, OrderStatus::DELIVERED, 7);
        $this->createOrders($otherSupplier->id, 10, OrderStatus::CANCELLED, 8);

        $response = $this->actingAs($supplier)->getJson(route('metrics.supplier.general-metrics'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'orders' => [
                        'count_pending_orders' => 5,
                        'count_delivered_orders' => 5,
                        'count_cancelled_orders' => 5,
                        'total_sales' => '500.00'
                    ],
                    'products' => [
                        'count_all_products' => 15,
                        'count_available_products' => 5,
                        'count_out_of_stock_products' => 5,
                        'count_nearly_out_of_stock_products' => 5
                    ]
                ]
            ]);

        $response = $this->actingAs($otherSupplier)->getJson(route('metrics.supplier.general-metrics'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'orders' => [
                        'count_pending_orders' => 6,
                        'count_delivered_orders' => 7,
                        'count_cancelled_orders' => 8,
                        'total_sales' => '420.00'
                    ],
                    'products' => [
                        'count_all_products' => 9,
                        'count_available_products' => 6,
                        'count_out_of_stock_products' => 1,
                        'count_nearly_out_of_stock_products' => 2
                    ]
                ]
            ]);
    }

    public function test_non_suppliers_cannot_access_general_metrics()
    {
        $supplier = $this->createUser(UserRole::BUYER);

        $response = $this->actingAs($supplier)->getJson(route('metrics.supplier.general-metrics'));

        $response->assertStatus(401);
    }

    public function test_guest_cannot_access_general_metrics()
    {
        $response = $this->getJson(route('metrics.supplier.general-metrics'));
        $response->assertStatus(401);
    }

    public function test_supplier_with_no_data_returns_zero_metrics()
    {
        $supplier = $this->createUser(UserRole::SUPPLIER);

        $response = $this->actingAs($supplier)->getJson(route('metrics.supplier.general-metrics'));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'orders' => [
                        'count_pending_orders' => 0,
                        'count_delivered_orders' => 0,
                        'count_cancelled_orders' => 0,
                        'total_sales' => '0.00'
                    ],
                    'products' => [
                        'count_all_products' => 0,
                        'count_available_products' => 0,
                        'count_out_of_stock_products' => 0,
                        'count_nearly_out_of_stock_products' => 0
                    ]
                ]
            ]);
    }

    // helpers
    protected function createUser(UserRole $role, array $attributes = []): User
    {
        $supplier = User::factory()->create(array_merge([
            'role' => $role,
            'status' => UserStatus::APPROVED,
            'is_verified' => true,
        ], $attributes));

        return $supplier;
    }

    protected function createProducts(int $supplierId, int $stockQuantity, int $nearlyOutOfStockLimit = 0, int $count = 5)
    {
        Product::factory()->count($count)->create([
            'supplier_id' => $supplierId,
            'stock_qty' => $stockQuantity,
            'nearly_out_of_stock_limit' => $nearlyOutOfStockLimit,
        ]);
    }

    protected function createOrders(int $supplierId, float $total, OrderStatus $status, int $count = 5)
    {
        Order::factory()->count($count)->create([
            'supplier_id' => $supplierId,
            'status' => $status,
            'total' => $total
        ]);
    }
}

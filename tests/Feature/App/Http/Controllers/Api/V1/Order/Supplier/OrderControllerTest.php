<?php

namespace Tests\Feature\App\Http\Controllers\Api\V1\Order\Supplier;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Enums\UserRole;
use App\Models\Address;
use App\Enums\UserStatus;
use App\Enums\Order\OrderStatus;
use App\Dtos\OrderChartDto;
use App\Services\Order\OrderService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

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

    // ============================================
    // getOrdersTimeChart Tests
    // ============================================

    public function test_unauthenticated_user_cannot_access_orders_time_chart()
    {
        $response = $this->getJson(route('supplier.orders.time-chart'));

        $response->assertStatus(401);
    }

    public function test_buyer_cannot_access_orders_time_chart()
    {
        $buyer = $this->createUser(UserRole::BUYER);

        $response = $this->actingAs($buyer)->getJson(route('supplier.orders.time-chart'));

        $response->assertStatus(401);
    }

    public function test_supplier_can_get_monthly_orders_time_chart_default()
    {
        // Create orders for current month
        $this->createOrdersForCurrentMonth($this->supplier->id);

        $response = $this->actingAs($this->supplier)->getJson(route('supplier.orders.time-chart'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'period',
                        'count',
                        'date',
                    ],
                ],
            ])
            ->assertJson(['success' => true]);

        $data = $response->json('data');
        
        // Should return all days in current month
        $this->assertCount(now()->daysInMonth, $data);
        
        // Check first day format
        $this->assertMatchesRegularExpression('/^[A-Z][a-z]{2} \d{1,2}$/', $data[0]['period']); // "Jan 1" format
        $this->assertIsInt($data[0]['count']);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $data[0]['date']); // "2024-01-01" format
    }

    public function test_supplier_can_get_weekly_orders_time_chart()
    {
        // Create orders for current week
        $this->createOrdersForCurrentWeek($this->supplier->id);

        $response = $this->actingAs($this->supplier)->getJson(route('supplier.orders.time-chart', [
            'time_filter' => 'weekly'
        ]));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $data = $response->json('data');
        
        // Should return 7 days
        $this->assertCount(7, $data);
        
        // Check day names format
        $dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        foreach ($data as $index => $day) {
            $this->assertContains($day['period'], $dayNames);
            $this->assertIsInt($day['count']);
            $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $day['date']);
        }
    }

    public function test_supplier_can_get_yearly_orders_time_chart()
    {
        // Create orders for current year
        $this->createOrdersForCurrentYear($this->supplier->id);

        $response = $this->actingAs($this->supplier)->getJson(route('supplier.orders.time-chart', [
            'time_filter' => 'yearly'
        ]));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $data = $response->json('data');
        
        // Should return 12 months
        $this->assertCount(12, $data);
        
        // Check month names format
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        foreach ($data as $index => $month) {
            $this->assertContains($month['period'], $monthNames);
            $this->assertIsInt($month['count']);
            $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $month['date']);
        }
    }

    public function test_supplier_can_filter_orders_time_chart_by_status()
    {
        // Create orders with different statuses
        $this->createOrdersWithStatus($this->supplier->id, OrderStatus::PENDING, 3);
        $this->createOrdersWithStatus($this->supplier->id, OrderStatus::ACCEPTED, 2);

        // Get chart for pending orders only
        $response = $this->actingAs($this->supplier)->getJson(route('supplier.orders.time-chart', [
            'time_filter' => 'monthly',
            'status' => OrderStatus::PENDING->value
        ]));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $data = $response->json('data');
        
        // Sum all counts to verify only pending orders are counted
        $totalCount = array_sum(array_column($data, 'count'));
        $this->assertEquals(3, $totalCount);
    }

    public function test_supplier_only_sees_their_own_orders_in_time_chart()
    {
        // Create orders for this supplier
        $this->createOrdersForCurrentMonth($this->supplier->id, 5);
        
        // Create orders for other supplier
        $this->createOrdersForCurrentMonth($this->otherSupplier->id, 10);

        $response = $this->actingAs($this->supplier)->getJson(route('supplier.orders.time-chart'));

        $response->assertStatus(200);

        $data = $response->json('data');
        
        // Should only count this supplier's orders (5), not the other supplier's (10)
        $totalCount = array_sum(array_column($data, 'count'));
        $this->assertEquals(5, $totalCount);
    }

    public function test_orders_time_chart_returns_zero_counts_for_days_without_orders()
    {
        // Don't create any orders
        
        $response = $this->actingAs($this->supplier)->getJson(route('supplier.orders.time-chart', [
            'time_filter' => 'weekly'
        ]));

        $response->assertStatus(200);

        $data = $response->json('data');
        
        // Should return 7 days all with count 0
        $this->assertCount(7, $data);
        foreach ($data as $day) {
            $this->assertEquals(0, $day['count']);
        }
    }

    public function test_orders_time_chart_with_invalid_time_filter_uses_default()
    {
        $response = $this->actingAs($this->supplier)->getJson(route('supplier.orders.time-chart', [
            'time_filter' => 'invalid_filter'
        ]));

        // Should return validation error
        $response->assertStatus(422);
    }

    public function test_orders_time_chart_with_invalid_status_returns_validation_error()
    {
        $response = $this->actingAs($this->supplier)->getJson(route('supplier.orders.time-chart', [
            'status' => 'invalid_status'
        ]));

        $response->assertStatus(422);
    }


    public function test_weekly_chart_service_directly()
    {
        // Test the service directly to isolate the issue
        $startOfWeek = now()->startOfWeek();
        
        // Create order on Monday
        Order::factory()->create([
            'supplier_id' => $this->supplier->id,
            'created_at' => $startOfWeek->copy()->setTime(12, 0, 0),
        ]);

        // Call service directly
        $orderService = app(OrderService::class);
        $orderChartDto = new OrderChartDto('weekly', null);
        
        $result = $orderService->getOrdersTimeChart($this->supplier->id, $orderChartDto);
        
        // Debug output
        dump([
            'Service result' => $result,
            'Total count' => array_sum(array_column($result, 'count')),
        ]);
        
        $this->assertCount(7, $result);
        $this->assertEquals(1, array_sum(array_column($result, 'count')));
    }

    public function test_monthly_chart_covers_all_days_of_current_month()
    {
        $startOfMonth = now()->startOfMonth();
        $daysInMonth = $startOfMonth->daysInMonth;
        
        // Create order on 15th of current month
        Order::factory()->create([
            'supplier_id' => $this->supplier->id,
            'created_at' => $startOfMonth->copy()->day(15),
        ]);

        $response = $this->actingAs($this->supplier)->getJson(route('supplier.orders.time-chart', [
            'time_filter' => 'monthly'
        ]));

        $data = $response->json('data');
        
        // Should have entry for every day of the month
        $this->assertCount($daysInMonth, $data);
        
        // 15th day should have count 1 (index 14, since it's 0-based)
        $this->assertEquals(1, $data[14]['count']);
    }

    public function test_yearly_chart_covers_all_months_of_current_year()
    {
        // Create order in March (month 3)
        Order::factory()->create([
            'supplier_id' => $this->supplier->id,
            'created_at' => now()->startOfYear()->month(3)->day(15),
        ]);

        $response = $this->actingAs($this->supplier)->getJson(route('supplier.orders.time-chart', [
            'time_filter' => 'yearly'
        ]));

        $data = $response->json('data');
        
        // Should have 12 months
        $this->assertCount(12, $data);
        
        // March should have count 1 (index 2, since it's 0-based)
        $this->assertEquals(1, $data[2]['count']);
        $this->assertEquals('Mar', $data[2]['period']);
    }

    // ============================================
    // Helper Methods for Chart Tests
    // ============================================

    protected function createOrdersForCurrentMonth(int $supplierId, int $count = 3)
    {
        $startOfMonth = now()->startOfMonth();
        
        for ($i = 0; $i < $count; $i++) {
            Order::factory()->create([
                'supplier_id' => $supplierId,
                'created_at' => $startOfMonth->copy()->addDays($i * 2), // Spread across month
            ]);
        }
    }

    protected function createOrdersForCurrentWeek(int $supplierId, int $count = 3)
    {
        $startOfWeek = now()->startOfWeek();
        
        for ($i = 0; $i < $count; $i++) {
            Order::factory()->create([
                'supplier_id' => $supplierId,
                'created_at' => $startOfWeek->copy()->addDays($i), // Different days of week
            ]);
        }
    }

    protected function createOrdersForCurrentYear(int $supplierId, int $count = 6)
    {
        $startOfYear = now()->startOfYear();
        
        for ($i = 0; $i < $count; $i++) {
            Order::factory()->create([
                'supplier_id' => $supplierId,
                'created_at' => $startOfYear->copy()->addMonths($i)->day(15), // Different months
            ]);
        }
    }

    protected function createOrdersWithStatus(int $supplierId, OrderStatus $status, int $count)
    {
        Order::factory()->count($count)->create([
            'supplier_id' => $supplierId,
            'status' => $status,
            'created_at' => now(), // Current time for current period
        ]);
    }
}

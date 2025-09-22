<?php

namespace App\Services\Order;

use App\Dtos\OrderChartDto;
use App\Models\User;
use App\Models\Order;
use App\Dtos\OrderFilterDto;
use App\Exceptions\CartException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\AbstractPaginator;
use App\Services\Contracts\CartServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderService
{

    public function __construct(protected CartServiceInterface $cartService)
    {
        //
    }

    /**
     * Get the buyer orders.
     */
    public function getBuyerOrders(int $userId, OrderFilterDto $orderFilterDto): AbstractPaginator
    {
        return $this->baseOrderQuery($orderFilterDto)
            ->addSelect(['users.name as supplier_name', 'users.image as supplier_image'])
            ->with(['ratings'])
            ->leftJoin('users', 'users.id', '=', 'orders.supplier_id')
            ->forBuyer($userId)
            ->orderByDesc('orders.created_at')
            ->paginate(10);
    }

    /**
     * Get the supplier orders.
     */
    public function getSupplierOrders(int $userId, OrderFilterDto $orderFilterDto): AbstractPaginator
    {
        return $this->baseOrderQuery($orderFilterDto)
            ->addSelect(['users.name as buyer_name', 'order_details.tracking_number'])
            ->leftJoin('users', 'users.id', '=', 'orders.user_id')
            ->forSupplier($userId)
            ->orderByDesc('orders.created_at')
            ->paginate(10);
    }

    /**
     * Get the base order query.
     */
    private function baseOrderQuery(OrderFilterDto $orderFilterDto): Builder
    {
        return Order::query()
            ->select([
                'orders.id',
                'orders.status',
                'orders.order_type',
                'orders.user_id',
                'orders.supplier_id',
                'orders.total',
                'orders.total_discount',
                'orders.created_at',
                'order_details.shipping_method',
                'order_details.payment_status',
                'order_details.tracking_number',
            ])
            ->withCount('products')
            ->leftJoin('order_details', 'order_details.order_id', '=', 'orders.id')
            ->when($orderFilterDto->orderStatus ?? null, function ($query) use ($orderFilterDto) {
                $query->where('orders.status', $orderFilterDto->orderStatus);
            })
            ->when($orderFilterDto->shippingMethod ?? null, function ($query) use ($orderFilterDto) {
                $query->where('order_details.shipping_method', $orderFilterDto->shippingMethod);
            })
            ->when($orderFilterDto->startDate && $orderFilterDto->endDate, function ($query) use ($orderFilterDto) {
                $query->whereBetween('orders.created_at', [$orderFilterDto->startDate->startOfDay(), $orderFilterDto->endDate->endOfDay()]);
            });
    }

    public function reorder(Order $order, User $user): array
    {
        $addedCount = 0;
        $errors = [];
        $succeededProducts = [];

        $this->cartService->clearCart($user);

        foreach ($order->products as $product) {
            try {

                $this->cartService->addToCart($user, $product->product_id, $product->quantity);
                $addedCount++;
                $succeededProducts[] = [
                    'product_id' => $product->product_id,
                    'quantity' => $product->quantity,
                    'name' => $product->product->name ?? 'Unknown Product',
                ];

            } catch (CartException $e) {

                $errors[] = $e->getMessage();
            } catch (ModelNotFoundException $e) {

                $errors[] = 'Product not found';
            }
        }

        return [
            'success' => true,
            'added_count' => $addedCount,
            'errors' => $errors,
            'succeeded_products' => $succeededProducts,
        ];
    }

    /**
     * Get orders count grouped by time periods for chart visualization.
     */
    public function getOrdersTimeChart(int $supplierId, OrderChartDto $orderChartDto): array
    {
        // Get date range based on time filter
        [$startDate, $endDate] = $this->getDateRange($orderChartDto->timeFilter);
        
        // Build query
        $query = Order::query()
            ->where('supplier_id', $supplierId)
            ->whereBetween('created_at', [$startDate, $endDate]);

        // Apply status filter if provided
        if ($orderChartDto->status) {
            $query->where('status', $orderChartDto->status);
        }

        // Get chart data based on time filter
        return match ($orderChartDto->timeFilter) {
            'weekly' => $this->getWeeklyChart($query, $startDate),
            'yearly' => $this->getYearlyChart($query, $startDate),
            default => $this->getMonthlyChart($query, $startDate),
        };
    }

    /**
     * Get date range based on time filter.
     */
    private function getDateRange(string $timeFilter): array
    {
        return match ($timeFilter) {
            'weekly' => [now()->startOfWeek(), now()->endOfWeek()],
            'monthly' => [now()->startOfMonth(), now()->endOfMonth()],
            'yearly' => [now()->startOfYear(), now()->endOfYear()],
            default => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }

    /**
     * Get weekly chart data (current week by days).
     */
    private function getWeeklyChart($query, $startDate): array
    {
        $results = $query
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $chartData = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dateKey = $date->format('Y-m-d');
            
            $chartData[] = [
                'period' => $date->format('l'), 
                'count' => $results->get($dateKey)?->count ?? 0,
                'date' => $dateKey,
            ];
        }

        return $chartData;
    }

    /**
     * Get monthly chart data (current month by days).
     */
    private function getMonthlyChart($query, $startDate): array
    {
        $results = $query
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $chartData = [];
        $daysInMonth = $startDate->daysInMonth;
        
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = $startDate->copy()->day($i);
            $dateKey = $date->format('Y-m-d');
            
            $chartData[] = [
                'period' => $date->format('M j'), 
                'count' => $results->get($dateKey)?->count ?? 0,
                'date' => $dateKey,
            ];
        }

        return $chartData;
    }

    /**
     * Get yearly chart data (current year by months).
     */
    private function getYearlyChart($query, $startDate): array
    {
        $results = $query
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $date = $startDate->copy()->month($i);
            
            $chartData[] = [
                'period' => $date->format('M'), 
                'count' => $results->get($i)?->count ?? 0,
                'date' => $date->format('Y-m-d'),
            ];
        }

        return $chartData;
    }
}

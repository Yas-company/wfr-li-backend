<?php

namespace App\Services;

use App\Models\Order;
use App\Dtos\OrderFilterDto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\AbstractPaginator;

class OrderService
{
    /**
     * Get the buyer orders.
     *
     * @param int $userId
     * @param OrderFilterDto $orderFilterDto
     *
     * @return AbstractPaginator
     */
    public function getBuyerOrders(int $userId, OrderFilterDto $orderFilterDto): AbstractPaginator
    {
        return $this->baseOrderQuery($orderFilterDto)
            ->addSelect(['users.name as supplier_name'])
            ->with(['ratings'])
            ->leftJoin('users', 'users.id', '=', 'orders.supplier_id')
            ->forBuyer($userId)
            ->orderByDesc('orders.created_at')
            ->paginate(10);
    }

    /**
     * Get the supplier orders.
     *
     * @param int $userId
     * @param OrderFilterDto $orderFilterDto
     *
     * @return AbstractPaginator
     */
    public function getSupplierOrders(int $userId, OrderFilterDto $orderFilterDto): AbstractPaginator
    {
        return $this->baseOrderQuery($orderFilterDto)
            ->addSelect(['users.name as buyer_name'])
            ->leftJoin('users', 'users.id', '=', 'orders.user_id')
            ->forSupplier($userId)
            ->orderByDesc('orders.created_at')
            ->paginate(10);
    }

    /**
     * Get the base order query.
     *
     * @param OrderFilterDto $orderFilterDto
     *
     * @return Builder
     */
    private function baseOrderQuery(OrderFilterDto $orderFilterDto): Builder
    {
        return Order::query()
            ->select([
                'orders.id',
                'orders.status',
                'orders.user_id',
                'orders.supplier_id',
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
}

<?php

namespace App\Services\Order;

use App\Enums\Order\OrderStatus;
use App\Exceptions\OrderException;
use App\Models\Order;

class OrderStatusService
{
    protected array $allowedTransitions = [
        OrderStatus::ACCEPTED->value => [OrderStatus::SHIPPED],
        OrderStatus::SHIPPED->value => [OrderStatus::DELIVERED],
    ];

    /**
     * Change the order status.
     *
     * @param Order $order
     * @param OrderStatus $newStatus
     *
     * @return Order
     */
    public function changeOrderStatus(Order $order, OrderStatus $newStatus): Order
    {
        if (!$this->canTransition($order->status, $newStatus)) {
            throw OrderException::invalidTransition();
        }

        $order->update(['status' => $newStatus]);

        return $order;
    }

    /**
     * Check if the transition is allowed.
     *
     * @param OrderStatus $currentStatus
     * @param OrderStatus $newStatus
     *
     * @return bool
     */
    protected function canTransition(OrderStatus $currentStatus, OrderStatus $newStatus): bool
    {
        return in_array($newStatus, $this->allowedTransitions[$currentStatus->value] ?? [], true);
    }
}

<?php

namespace App\Services\Order;

use App\Dtos\OrderFilterDto;
use App\Exceptions\CartException;
use App\Models\Order;
use App\Models\User;
use App\Services\Contracts\CartServiceInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\AbstractPaginator;

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
}

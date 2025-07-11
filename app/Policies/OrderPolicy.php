<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAsBuyer(User $user, Order $order): bool
    {
        return $user->isBuyer() &&$user->id === $order->user_id;
    }

    public function viewAsSupplier(User $user, Order $order): bool
    {
        return $user->isSupplier() && $user->id === $order->supplier_id;
    }
}

<?php

namespace App\Services\Contracts;

use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use App\Dtos\CartCheckoutDto;

interface CartServiceInterface
{
    public function getCart(User $user): Cart;
    public function addToCart(User $user, int $productId, int $quantity = 1): bool;
    public function removeFromCart(User $user, int $productId): bool;
    public function clearCart(User $user): bool;
    public function checkout(User $user, CartCheckoutDto $checkoutData): Order;
}

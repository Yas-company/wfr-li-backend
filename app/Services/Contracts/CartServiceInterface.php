<?php

namespace App\Services\Contracts;

use App\Models\Cart;
use App\Models\User;

interface CartServiceInterface
{
    public function getCart(User $user): Cart;
    public function addToCart(User $user, int $productId, int $quantity = 1): Cart;
    public function removeFromCart(User $user, int $productId): Cart;
    public function clearCart(User $user): Cart;
}

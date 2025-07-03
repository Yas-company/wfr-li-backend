<?php

namespace App\Contracts;

use App\Models\Cart;
use App\Models\Product;

interface CartValidatorInterface
{
    public function validateAddToCart(Cart $cart, Product $product, int $quantity = 1): void;
}

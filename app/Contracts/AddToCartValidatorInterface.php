<?php

namespace App\Contracts;

use App\Models\Cart;
use App\Models\Product;

interface AddToCartValidatorInterface
{
    public function validateAdd(Cart $cart, Product $product, ?int $quantity = null): void;
}

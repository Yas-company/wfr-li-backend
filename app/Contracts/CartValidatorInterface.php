<?php

namespace App\Contracts;

use App\Models\Cart;
use App\Models\Product;

interface CartValidatorInterface
{
    public function validateAdd(Cart $cart, Product $product, ?int $quantity = null): void;

    public function validateCheckout(Cart $cart): void;
}

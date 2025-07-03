<?php

namespace App\Validators;

use App\Contracts\CartValidatorInterface;
use App\Exceptions\CartException;
use App\Models\Cart;
use App\Models\Product;

class StockAvailabilityValidator implements CartValidatorInterface
{

    /**
     * @param Cart $cart
     * @param Product $product
     */
    public function validateAddToCart(Cart $cart, Product $product, int $quantity = 1): void
    {
        if ($product->stock_qty < $quantity) {
            throw CartException::insufficientStock();
        }
    }
}

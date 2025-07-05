<?php

namespace App\Validators;

use App\Contracts\AddToCartValidatorInterface;
use App\Exceptions\CartException;
use App\Models\Cart;
use App\Models\Product;

class StockAvailabilityValidator implements AddToCartValidatorInterface
{

    /**
     * @param Cart $cart
     * @param Product $product
     */
    public function validateAdd(Cart $cart, Product $product, ?int $quantity = null): void
    {
        if ($product->stock_qty < $quantity) {
            throw CartException::insufficientStock();
        }
    }
}

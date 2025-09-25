<?php

namespace App\Validators;

use App\Models\Cart;
use App\Models\Product;
use App\Exceptions\CartException;
use App\Contracts\AddToCartValidatorInterface;
use App\Contracts\CheckoutCartValidatorInterface;

class StockAvailabilityValidator implements AddToCartValidatorInterface, CheckoutCartValidatorInterface
{

    /**
     * @param Cart $cart
     * @param Product $product
     * @param int|null $quantity
     *
     * @throws CartException
     */
    public function validateAdd(Cart $cart, Product $product, ?int $quantity = null): void
    {

        if ($product->stock_qty < $quantity) {
            throw CartException::insufficientStock();
        }
    }

    /**
     * @param Cart $cart
     *
     * @throws CartException
     */
    public function validateCheckout(Cart $cart): void
    {
        foreach ($cart->products as $item) {
            $product = $item->product;
            if ($product->stock_qty < $item->quantity) {
                throw CartException::insufficientStock();
            }
        }
    }
}

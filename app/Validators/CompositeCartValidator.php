<?php

namespace App\Validators;

use App\Models\Cart;
use App\Models\Product;
use App\Exceptions\CartException;
use App\Contracts\AddToCartValidatorInterface;
use App\Contracts\CheckoutCartValidatorInterface;

class CompositeCartValidator implements AddToCartValidatorInterface, CheckoutCartValidatorInterface
{
    /**
     * @param AddToCartValidatorInterface[] $validators
     */
    public function __construct(protected iterable $validators)
    {
        //
    }

    public function validateAdd(Cart $cart, Product $product, ?int $quantity = null): void
    {
        foreach ($this->validators as $validator) {
            $validator->validateAdd($cart, $product, $quantity);
        }
    }

    public function validateCheckout(Cart $cart): void
    {
        if ($cart->products->isEmpty()) {
            throw CartException::emptyCart();
        }

        foreach ($cart->products as $item) {
            $this->validateAdd($cart, $item->product, $item->quantity);
        }
    }
}

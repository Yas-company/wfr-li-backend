<?php

namespace App\Validators;

use App\Contracts\CartValidatorInterface;
use App\Models\Cart;
use App\Models\Product;

class CompositeCartValidator implements CartValidatorInterface
{
    /**
     * @param CartValidatorInterface[] $validators
     */
    public function __construct(protected iterable $validators)
    {
        //
    }

    public function validateAddToCart(Cart $cart, Product $product, int $quantity = 1): void
    {
        foreach ($this->validators as $validator) {
            $validator->validateAddToCart($cart, $product, $quantity);
        }
    }
}

<?php

namespace App\Validators;

use App\Models\Cart;
use App\Models\Product;
use App\Contracts\CartValidatorInterface;
class CompositeCartValidator implements CartValidatorInterface
{
    /**
     * @param $addToCartValidators
     * @param $checkoutValidators
     */
    public function __construct(
        protected array $addToCartValidators,
        protected array $checkoutValidators
    )
    {
        //
    }

    /**
     * Validate the add to cart.
     *
     * @param Cart $cart
     * @param Product $product
     * @param int|null $quantity
     *
     * @throws CartException
     */
    public function validateAdd(Cart $cart, Product $product, ?int $quantity = null): void
    {
        foreach ($this->addToCartValidators as $validator) {
            $validator->validateAdd($cart, $product, $quantity);
        }
    }

    /**
     * Validate the checkout.
     *
     * @param Cart $cart
     *
     * @throws CartException
     */
    public function validateCheckout(Cart $cart): void
    {
        foreach ($this->checkoutValidators as $validator) {
            $validator->validateCheckout($cart);
        }
    }
}

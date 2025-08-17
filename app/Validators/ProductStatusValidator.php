<?php

namespace App\Validators;

use App\Models\Cart;
use App\Models\Product;
use App\Enums\ProductStatus;
use App\Exceptions\CartException;
use App\Contracts\AddToCartValidatorInterface;
use App\Contracts\CheckoutCartValidatorInterface;

class ProductStatusValidator implements AddToCartValidatorInterface, CheckoutCartValidatorInterface
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
        $this->validateProduct($product);
    }

    /**
     * @param Cart $cart
     *
     * @throws CartException
     */
    public function validateCheckout(Cart $cart): void
    {
        foreach ($cart->products as $item) {
            $this->validateProduct($item->product);
        }
    }

    /**
     * @param Product $product
     *
     * @throws CartException
     */
    private function validateProduct(Product $product): void
    {
        if (
            !$product->is_active ||
            $product->status !== ProductStatus::PUBLISHED
        ) {
            throw CartException::invalidProduct();
        }
    }
}

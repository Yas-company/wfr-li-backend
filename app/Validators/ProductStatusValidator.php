<?php

namespace App\Validators;

use App\Contracts\AddToCartValidatorInterface;
use App\Enums\ProductStatus;
use App\Exceptions\CartException;
use App\Models\Cart;
use App\Models\Product;

class ProductStatusValidator implements AddToCartValidatorInterface
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
        if (
            !$product->is_active ||
            $product->status !== ProductStatus::PUBLISHED
        ) {
            throw CartException::invalidProduct();
        }
    }
}

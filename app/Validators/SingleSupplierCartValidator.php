<?php

namespace App\Validators;

use App\Contracts\AddToCartValidatorInterface;
use App\Models\Product;
use App\Exceptions\CartException;
use App\Models\Cart;

class SingleSupplierCartValidator implements AddToCartValidatorInterface
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
        if (!$cart || $cart->products->isEmpty()) {
            return;
        }

        $currentSupplierId = $cart->products->first()->product->supplier_id;

        if ($product->supplier_id !== $currentSupplierId) {
            throw CartException::cannotMixProductsFromDifferentSuppliers();
        }
    }
}

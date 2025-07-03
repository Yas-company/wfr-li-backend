<?php

namespace App\Validators;

use App\Models\Product;
use App\Exceptions\CartException;
use App\Contracts\CartValidatorInterface;
use App\Models\Cart;

class SingleSupplierCartValidator implements CartValidatorInterface
{

    /**
     * @param Cart $cart
     * @param Product $product
     */
    public function validateAddToCart(Cart $cart, Product $product, int $quantity = 1): void
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

<?php

namespace App\Validators;

use App\Models\Cart;
use App\Models\Product;
use App\Exceptions\CartException;
use App\Contracts\AddToCartValidatorInterface;
use App\Contracts\CheckoutCartValidatorInterface;

class SingleSupplierCartValidator implements AddToCartValidatorInterface, CheckoutCartValidatorInterface
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

    public function validateCheckout(Cart $cart): void
    {
        $currentSupplierId = $cart->products->first()->product->supplier_id;

        foreach ($cart->products as $item) {
            $product = $item->product;
            if ($product->supplier_id !== $currentSupplierId) {
                throw CartException::cannotMixProductsFromDifferentSuppliers();
            }
        }
    }
}

<?php

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\Product;
use App\Exceptions\CartException;

class CartProductManager
{
    /**
     * Add a product to the cart
     *
     * @param Cart $cart
     * @param Product $product
     *
     */
    public function add(Cart $cart, Product $product, int $quantity): void
    {
        $cart->products()->updateOrCreate([
            'product_id' => $product->id,
        ], [
            'quantity' => $quantity,
            'price' => $product->price,
        ]);
    }

    /**
     * Remove a product from the cart
     *
     * @param Cart $cart
     * @param int $productId
     */
    public function remove(Cart $cart, int $productId): void
    {
        $cart->products()
            ->where('product_id', $productId)
            ->delete();
    }

    /**
     * Clear all products from the cart
     *
     * @param Cart $cart
     */
    public function clear(Cart $cart): void
    {
        $cart->products()->delete();
    }
}

<?php

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\Product;
use App\Exceptions\CartException;

class CartProductManager
{
    public function add(Cart $cart, Product $product, int $quantity)
    {
        if ($product->stock_qty < $quantity) {
            throw CartException::insufficientStock();
        }

        $cart->products()->updateOrCreate([
            'product_id' => $product->id,
        ], [
            'quantity' => $quantity,
            'price' => $product->price,
        ]);
    }

    public function remove(Cart $cart, int $productId)
    {
        $cart->products()
            ->where('product_id', $productId)
            ->delete();
    }

    public function clear(Cart $cart)
    {
        $cart->products()->delete();
    }
}

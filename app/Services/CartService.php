<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\User;
use App\Models\Product;
use App\Exceptions\CartException;

class CartService
{

    /**
     * Get the cart for the user.
     *
     * @param User $user
     *
     * @return Cart
     */
    public function getCart(User $user): Cart
    {
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        return $cart->load('products');
    }

    /**
     * Add a product to the cart.
     *
     * @param User $user
     * @param int $productId
     * @param int $quantity
     *
     * @return Cart
     *
     * @throws CartException
     */
    public function addToCart(User $user, int $productId, int $quantity = 1): Cart
    {
        $product = Product::findOrFail($productId);
        $cart = $this->getCart($user);

        if($product->stock_qty < $quantity){
            throw CartException::insufficientStock();
        }

        $cart->products()->updateOrCreate(
            [
                'product_id' => $product->id,
                'cart_id' => $cart->id
            ],
            [
                'quantity' => $quantity,
                'price' => $product->price
            ]
        );

        return $this->getCart($user);
    }

    /**
     * Remove a product from the cart.
     *
     * @param User $user
     * @param int $productId
     *
     * @return Cart
     */
    public function removeFromCart(User $user, int $productId): Cart
    {
        $cart = $this->getCart($user);

        $cart->products()
            ->where('product_id', $productId)
            ->delete();

        return $this->getCart($user);
    }

    /**
     * Clear all products from the cart.
     *
     * @param User $user
     *
     * @return Cart
     */
    public function clearCart(User $user): Cart
    {
        $cart = $this->getCart($user);
        $cart->products()->delete();

        return $cart->refresh();
    }

    /**
     * Get the total price of the cart.
     *
     * @param User $user
     *
     * @return float
     */
    public function getCartTotal(User $user): float
    {
        $cart = $this->getCart($user);

        return $cart->products->sum(function ($products) {
            return $products->quantity * $products->price;
        });
    }

    /**
     * Get the total price of the cart before discount.
     *
     * @param User $user
     *
     * @return float
     */
    public function getCartTotalBeforeDiscount(User $user): float
    {
        $cart = $this->getCart($user);

        return $cart->products->sum(function ($products) {
            return $products->quantity * $products->product->price_before_discount;
        });
    }
}

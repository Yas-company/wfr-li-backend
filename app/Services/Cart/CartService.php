<?php

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\User;
use App\Models\Product;
use App\Values\CartTotals;
use App\Exceptions\CartException;
use App\Services\Contracts\CartServiceInterface;

class CartService implements CartServiceInterface
{

    public function __construct(private CartProductManager $cartProductManager)
    {
        //
    }

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

        $this->cartProductManager->add($cart, $product, $quantity);

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

        $this->cartProductManager->remove($cart, $productId);

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

        $this->cartProductManager->clear($cart);

        return $cart->refresh();
    }

    /**
     * Get the total price of the cart.
     *
     * @param User $user
     *
     * @return CartTotals
     */
    public function getCartTotals(User $user): CartTotals
    {
        $cart = $this->getCart($user);

        return CartTotals::fromProducts($cart->products);
    }
}

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

    protected ?Cart $cart = null;

    /**
     * CartService constructor.
     *
     * @param CartProductManager $cartProductManager
     */
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
        if ($this->cart) {
            return $this->cart;
        }

        $this->cart = Cart::with('products.product')->firstOrCreate(['user_id' => $user->id]);

        return $this->cart;
    }

    /**
     * Add a product to the cart.
     *
     * @param User $user
     * @param int $productId
     * @param int $quantity
     *
     * @return bool
     *
     * @throws CartException
     */
    public function addToCart(User $user, int $productId, int $quantity = 1): bool
    {
        $product = Product::findOrFail($productId);
        $cart = $this->getCart($user);

        $this->cartProductManager->add($cart, $product, $quantity);

        return true;
    }

    /**
     * Remove a product from the cart.
     *
     * @param User $user
     * @param int $productId
     *
     * @return bool
     */
    public function removeFromCart(User $user, int $productId): bool
    {
        $cart = $this->getCart($user);

        $this->cartProductManager->remove($cart, $productId);

        return true;
    }

    /**
     * Clear all products from the cart.
     *
     * @param User $user
     *
     * @return bool
     */
    public function clearCart(User $user): bool
    {
        $cart = $this->getCart($user);

        $this->cartProductManager->clear($cart);

        return true;
    }

    /**
     * Get the total price of the cart.
     *
     * @param Cart $cart
     *
     * @return CartTotals
     */
    public function getCartTotals(Cart $cart): CartTotals
    {
        return CartTotals::fromProducts($cart->products);
    }
}

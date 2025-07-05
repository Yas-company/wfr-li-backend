<?php

namespace App\Services\Cart;

use App\Contracts\AddToCartValidatorInterface;
use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Values\CartTotals;
use App\Models\OrderDetail;
use App\Exceptions\CartException;
use Illuminate\Support\Facades\DB;
use App\Services\Contracts\CartServiceInterface;

class CartService implements CartServiceInterface
{

    protected ?Cart $cart = null;

    /**
     * CartService constructor.
     *
     * @param CartProductManager $cartProductManager
     */
    public function __construct(
        private CartProductManager $cartProductManager,
        protected AddToCartValidatorInterface $cartValidator
    )
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

        $this->cartValidator->validateAdd($cart, $product, $quantity);

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


    /**
     * Checkout the cart.
     *
     * @param User $user
     * @param array $checkoutData
     *
     * @return Order
     */
    public function checkout(User $user, array $checkoutData): Order
    {
        $cart = $this->getCart($user);
        $this->cartValidator->validateCheckout($cart);


        return DB::transaction(function() use ($cart, $user, $checkoutData) {

            $totals = $this->getCartTotals($cart);

            $cartProductIds = $cart->products->pluck('product_id')->toArray();
            Product::whereIn('id', $cartProductIds)->lockForUpdate()->get();


            $order = Order::create([
                'user_id' => $user->id,
                'total' => $totals->total,
                'total_discount' => $totals->discount,
                'status' => 'pending',
            ]);

            OrderDetail::create([
                'order_id' => $order->id,
                'shipping_address' => $checkoutData['shipping_address'],
                'shipping_latitude' => $checkoutData['shipping_latitude'],
                'shipping_longitude' => $checkoutData['shipping_longitude'],
                'payment_method' => $checkoutData['payment_method'],
                'notes' => $checkoutData['notes'],
            ]);

            $orderProducts = $cart->products->mapWithKeys(fn($item) => [
                $item->product_id => [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ]
            ])->toArray();
            $order->products()->createMany($orderProducts);

            foreach ($cart->products as $item) {
                Product::where('id', $item->product_id)
                    ->decrement('stock_qty', $item->quantity);
            }

            $this->cartProductManager->clear($cart);

            return $order->load(['products', 'orderDetail']);
        });
    }
}

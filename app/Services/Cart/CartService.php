<?php

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Values\CartTotals;
use App\Models\OrderDetail;
use App\Exceptions\CartException;
use Illuminate\Support\Facades\DB;
use App\Enums\Settings\OrderSettings;
use App\Services\OrderTrackingService;
use App\Values\CartSupplierRequirement;
use App\Contracts\CartValidatorInterface;
use App\Http\Services\Payment\PaymentContext;
use App\Http\Services\Payment\PaymentFactory;
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
        protected CartValidatorInterface $cartValidator,
        protected OrderTrackingService $orderTrackingService
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
     * Get the supplier requirements.
     *
     * @param Cart $cart
     *
     * @return array
     */
    public function getSupplierRequirements(Cart $cart): array
    {
        $requirements = [];

        foreach ($cart->products as $item) {
            $supplierId = $item->product->supplier_id;
            $supplierName = $item->product->supplier->name;

            if (!isset($requirements[$supplierId])) {
                $requirements[$supplierId] = [
                    'supplier_id' => $supplierId,
                    'supplier_name' => $supplierName,
                    'required_amount' => $this->getMinOrderAmountForSupplier($supplierId),
                    'current_total' => 0,
                ];
            }

            $requirements[$supplierId]['current_total'] += $item->quantity * $item->price;
        }

        $final = [];
        foreach ($requirements as $req) {
            $requirement = new CartSupplierRequirement(
                $req['supplier_id'],
                $req['supplier_name'],
                $req['required_amount'],
                $req['current_total']
            );

            $final[] = $requirement->toArray();
        }

        return $final;
    }

    /**
     * Get the min order amount for the supplier.
     *
     * @param int $supplierId
     *
     * @return float
     */
    protected function getMinOrderAmountForSupplier(int $supplierId): float
    {
        $user = User::find($supplierId);

        return (float) $user?->setting(OrderSettings::ORDER_MIN_ORDER_AMOUNT->value, 0);
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

        $strategy = PaymentFactory::make($checkoutData['payment_method']);
        $context = new PaymentContext();
        $context->setStrategy($strategy);

        return DB::transaction(function() use ($cart, $user, $checkoutData,$context) {

            $totals = $this->getCartTotals($cart);
            $supplierId = $cart->products->first()->product->supplier_id;
            $cartProductIds = $cart->products->pluck('product_id')->toArray();
            Product::whereIn('id', $cartProductIds)->lockForUpdate()->get();

            $order = Order::create([
                'user_id' => $user->id,
                'total' => $totals->total,
                'total_discount' => $totals->discount,
                'status' => 'pending',
                'supplier_id' => $supplierId,
            ]);

           $pyment_id =  $context->createPayment($checkoutData, $totals->discount);

            OrderDetail::create([
                'order_id' => $order->id,
                'shipping_address_id' => $checkoutData['shipping_address_id'],
                'payment_method' => $checkoutData['payment_method'],
                'notes' => $checkoutData['notes'],
                'payment_id' => $pyment_id,
                'shipping_method' => $checkoutData['shipping_method'],
                'tracking_number' => $this->orderTrackingService->generateTrackingNumber($user->id, $supplierId, $order->id),
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

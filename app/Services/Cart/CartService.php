<?php

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Values\CartTotals;
use App\Models\OrderDetail;
use App\Dtos\CartCheckoutDto;
use App\Enums\Order\OrderType;
use App\Enums\Order\OrderStatus;
use App\Exceptions\CartException;
use Illuminate\Support\Facades\DB;
use App\Enums\Settings\OrderSettings;
use App\Services\OrderTrackingService;
use App\Values\CartSupplierRequirement;
use App\Contracts\CartValidatorInterface;
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

        $this->cart = Cart::with([
            'products.product',
            'products.product.media',
            'products.product.supplier'
        ])->firstOrCreate(['user_id' => $user->id]);

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
            $supplierImage = $item->product->supplier->image;

            if (!isset($requirements[$supplierId])) {
                $requirements[$supplierId] = [
                    'supplier_id' => $supplierId,
                    'supplier_name' => $supplierName,
                    'supplier_image' => $supplierImage,
                    'required_amount' => $this->getMinOrderAmountForSupplier($supplierId),
                    'current_total' => 0,
                ];
            }

            $requirements[$supplierId]['current_total'] += $item->quantity * $item->product->price;
        }

        $final = [];
        foreach ($requirements as $req) {
            $requirement = new CartSupplierRequirement(
                $req['supplier_id'],
                $req['supplier_name'],
                $req['required_amount'],
                $req['current_total'],
                $req['supplier_image'],
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
     * @param CartCheckoutDto $cartCheckoutDto
     *
     * @return Order
     */
    public function checkout(User $user, CartCheckoutDto $cartCheckoutDto): Order
    {
        if(
            OrderType::tryFrom($cartCheckoutDto->orderType) === OrderType::ORGANIZATION &&
            ! $user->isOrganization()
        ) {
            throw CartException::orderTypeNotAllowed();
        }

        $cart = $this->getCart($user);
        $this->cartValidator->validateCheckout($cart);

        return DB::transaction(function() use ($cart, $user, $cartCheckoutDto) {

            $totals = $this->getCartTotals($cart);
            $supplierId = $cart->products->first()->product->supplier_id;
            $cartProductIds = $cart->products->pluck('product_id')->toArray();
            Product::whereIn('id', $cartProductIds)->get();

            $order = Order::create([
                'user_id' => $user->id,
                'total' => $totals->total,
                'total_products' => $totals->productsSum,
                'total_discount' => $totals->discount,
                'status' => OrderStatus::PENDING,
                'supplier_id' => $supplierId,
                'order_type' => $cartCheckoutDto->orderType,
            ]);

            OrderDetail::create([
                'order_id' => $order->id,
                'shipping_address_id' => $cartCheckoutDto->shippingAddressId,
                'payment_method' => $cartCheckoutDto->paymentMethod,
                'notes' => $cartCheckoutDto->notes,
                'shipping_method' => $cartCheckoutDto->shippingMethod,
                'tracking_number' => $this->orderTrackingService->generateTrackingNumber($user->id, $supplierId, $order->id),
            ]);

            $orderProducts = $cart->products->mapWithKeys(fn($item) => [
                $item->product_id => [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price_after_taxes,
                ]
            ])->toArray();

            $order->products()->createMany($orderProducts);

            $this->cartProductManager->clear($cart);

            return $order->load(['products', 'orderDetail']);
        });
    }
}

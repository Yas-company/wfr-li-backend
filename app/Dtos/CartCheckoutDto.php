<?php

namespace App\Dtos;

use Illuminate\Http\Request;


final readonly class CartCheckoutDto
{
    /**
     * CartCheckoutDto Constructor
     *
     * @param string $shippingAddressId
     * @param string $paymentMethod
     * @param string $shippingMethod
     * @param string $orderType
     * @param string|null $notes
     */
    public function __construct(
        public readonly string $shippingAddressId,
        public readonly string $paymentMethod,
        public readonly string $shippingMethod,
        public readonly string $orderType,
        public readonly ?string $notes = null,
    ) {
    }

    /**
     * Create a new CartCheckoutDto instance from a request.
     *
     * @param Request $request
     *
     * @return CartCheckoutDto
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            shippingAddressId: $request->get('shipping_address_id'),
            paymentMethod: $request->get('payment_method'),
            shippingMethod: $request->get('shipping_method'),
            orderType: $request->get('order_type'),
            notes: $request->get('notes'),
        );
    }
}

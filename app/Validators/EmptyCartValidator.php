<?php

namespace App\Validators;

use App\Models\Cart;
use App\Contracts\CheckoutCartValidatorInterface;
use App\Exceptions\CartException;

class EmptyCartValidator implements CheckoutCartValidatorInterface
{
    /**
     * Validate the checkout.
     *
     * @param Cart $cart
     *
     * @throws CartException
     */
    public function validateCheckout(Cart $cart): void
    {
        if ($cart->products->isEmpty()) {
            throw CartException::emptyCart();
        }

        foreach($cart->products as $item)
        {
            if($item->quantity > 0)
            {
                return;
            }
        }

        throw CartException::emptyCart();
    }
}

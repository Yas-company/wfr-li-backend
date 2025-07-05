<?php

namespace App\Contracts;

use App\Models\Cart;

interface CheckoutCartValidatorInterface
{
    public function validateCheckout(Cart $cart): void;
}

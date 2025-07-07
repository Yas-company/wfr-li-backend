<?php

namespace App\Validators;

use App\Models\Cart;
use App\Exceptions\CartException;
use App\Contracts\CheckoutCartValidatorInterface;
use App\Enums\Settings\OrderSettings;

class MinOrderAmountValidator implements CheckoutCartValidatorInterface
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
        $supplierTotals = [];

        foreach ($cart->products as $item) {
            $supplierId = $item->product->supplier_id;

            if (!isset($supplierTotals[$supplierId])) {
                $supplierTotals[$supplierId] = 0;
            }

            $supplierTotals[$supplierId] += $item->quantity * $item->price;
        }

        foreach ($supplierTotals as $supplierId => $total) {
            $supplier = \App\Models\User::find($supplierId);

            if (!$supplier) continue;

            $minOrderAmount = (float) $supplier->setting(OrderSettings::ORDER_MIN_ORDER_AMOUNT->value, 0);

            if ($total < $minOrderAmount) {
                throw CartException::insufficientMinOrderAmount($supplier->name, $minOrderAmount);
            }
        }
    }
}

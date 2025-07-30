<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class CartException extends Exception
{
    public static function insufficientStock(): self
    {
        return new self(__('messages.cart.insufficient_stock'), Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public static function cannotMixProductsFromDifferentSuppliers(): self
    {
        return new self(__('messages.cart.cannot_mix_products_from_different_suppliers'), Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public static function emptyCart(): self
    {
        return new self(__('messages.cart.empty'), Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public static function productUnavailable(): self
    {
        return new self(__('messages.cart.item_not_found'), Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public static function invalidProduct(): self
    {
        return new self(__('messages.cart.invalid_product'), Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public static function insufficientMinOrderAmount(string $supplierName, float $minOrderAmount): self
    {
        return new self(
            message: __(
                'messages.cart.insufficient_min_order_amount',
                ['supplier_name' => $supplierName, 'min_order_amount' => $minOrderAmount]
            ),
            code: Response::HTTP_UNPROCESSABLE_ENTITY
        );
}
}

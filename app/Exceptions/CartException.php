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
}

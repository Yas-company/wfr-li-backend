<?php

namespace App\Exceptions;

use Exception;

class CartException extends Exception
{
    public static function insufficientStock(): self
    {
        return new self(__('messages.cart.insufficient_stock'));
    }
}

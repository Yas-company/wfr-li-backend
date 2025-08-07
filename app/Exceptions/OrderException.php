<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;


class OrderException extends Exception
{
    public static function invalidTransition(): self
    {
        return new self(__('messages.orders.invalid_transition'), Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}

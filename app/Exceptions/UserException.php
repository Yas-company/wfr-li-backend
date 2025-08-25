<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class UserException extends Exception
{
    public static function cannotDeleteLastAddress()
    {
        return new static(__('messages.users.cannot_delete_last_address'), Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public static function atLeastOneDefaultAddressRequired()
    {
        return new static(__('messages.users.at_least_one_default_address_required'), Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public static function cannotDeleteAddressAttachedToOrder()
    {
        return new static(__('messages.users.cannot_delete_address_attached_to_order'), Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}

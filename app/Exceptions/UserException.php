<?php

namespace App\Exceptions;

use Exception;

class UserException extends Exception
{
    public static function cannotDeleteLastAddress()
    {
        return new static(__('messages.users.cannot_delete_last_address'));
    }

    public static function atLeastOneDefaultAddressRequired()
    {
        return new static(__('messages.users.at_least_one_default_address_required'));
    }
}

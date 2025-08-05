<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class OrganizationException extends Exception
{
    public static function userAlreadyHasOrganization(string $organizationName): self
    {
        return new self("User already has a (pending\approved) organization named {$organizationName}",  Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}

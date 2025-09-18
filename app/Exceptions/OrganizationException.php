<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class OrganizationException extends Exception
{
    public static function userAlreadyHasOrganization(string $organizationName): self
    {
        return new self("User already has a (pending\approved) organization named {$organizationName}", Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public static function userDoesNotHaveOrganization(): self
    {
        return new self('User does not have an organization', Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}

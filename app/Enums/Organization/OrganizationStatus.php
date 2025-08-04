<?php

namespace App\Enums\Organization;

enum OrganizationStatus: int
{
    case PENDING = 1;
    case APPROVED = 2;
    case REJECTED = 3;
}

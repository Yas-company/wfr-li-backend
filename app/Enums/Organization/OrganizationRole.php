<?php

namespace App\Enums\Organization;

enum OrganizationRole: int
{
    case OWNER = 1;
    case MANAGER = 2;
    case MEMBER = 3;
}

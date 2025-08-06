<?php

namespace App\Enums\Organization;

use App\Traits\HasLabel;

enum OrganizationRole: int
{
    use HasLabel;

    case OWNER = 1;
    case MANAGER = 2;
    case MEMBER = 3;
}

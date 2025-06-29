<?php

namespace App\Enums;

enum ProductStatus: int
{
    case DRAFT = 0;
    case PUBLISHED = 1;
    case REJECTED = 2;
}

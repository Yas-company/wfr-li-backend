<?php

namespace App\Enums;

enum UnitType: int
{
    case PIECE = 0;
    case KG = 1;
    case G = 2;
    case LITER = 3;
    case ML = 4;
    case BOX = 5;
    case DOZEN = 6;
    case METER = 7;
}

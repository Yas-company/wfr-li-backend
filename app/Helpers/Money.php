<?php

if(! function_exists('to_percentage'))
{
    function to_percentage(float $total)
    {
        return $total / 100;
    }
}


if(! function_exists('to_base'))
{
    function to_base(float $total)
    {
        return round($total * 100, 2);
    }
}

if(! function_exists('money'))
{
    function money(float $total, int $precision = 0)
    {
        return round($total, $precision);
    }
}

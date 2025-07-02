<?php

namespace App\Http\Services\Contracts;

interface OrderServiceInterface
{
    public function checkout(array $data);
}

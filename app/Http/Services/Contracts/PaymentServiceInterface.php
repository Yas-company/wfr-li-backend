<?php

namespace App\Http\Services\Contracts;
interface PaymentServiceInterface
{
    public function storePayment($request,array $data);
}

<?php

namespace App\Http\Services\Contracts;
interface PaymentStrategyInterface
{
    public function createPayment(array $data,$totals_discount): int;
    public function verifyPayment(string $tap_id): array;

}

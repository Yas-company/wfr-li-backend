<?php

namespace App\Http\Services\Contracts;
interface PaymentStrategyInterface
{
    public function createPayment(array $data): array;
    public function verifyPayment(string $tap_id): array;

}

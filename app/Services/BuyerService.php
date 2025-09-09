<?php

namespace App\Services;

use App\Repositories\Suppliers\BuyerRepository;

class BuyerService
{
    public function __construct(protected BuyerRepository $buyerRepository)
    {
        //
    }

    public function getRelatedBuyers($data, $supplier)
    {
        return $this->buyerRepository->getRelatedBuyers($data, $supplier);
    }
}

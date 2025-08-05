<?php

namespace App\Services\Contracts;

use App\Models\Product;
use App\Models\User;

interface ProductServiceInterface
{
    public function getSupplierProducts(int $supplierId);

    public function getProductsForBuyer();

    public function store(array $data, User $user);

    public function update(Product $product, array $data);

    public function delete(Product $product);

    public function countTotal(User $user): int;

    public function countExpired(User $user): int;

    public function countNearExpiry(User $user): int;

    public function attachMedia(Product $product);

    public function getAvailableProducts(int $supplierId): \Illuminate\Pagination\LengthAwarePaginator;

    public function getNearlyOutOfStockProducts(int $supplierId): \Illuminate\Pagination\LengthAwarePaginator;

    public function getOutOfStockProducts(int $supplierId): \Illuminate\Pagination\LengthAwarePaginator;

    public function getSimilarProducts(Product $product);
}

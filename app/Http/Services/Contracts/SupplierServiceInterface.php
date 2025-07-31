<?php

namespace App\Http\Services\Contracts;

interface SupplierServiceInterface
{
    public function getAds(int $supplierId);

    public function getCategories(int $supplierId);

    public function getProducts(array $filters): \Illuminate\Contracts\Pagination\LengthAwarePaginator;

    public function getProductById(int $id): ?\App\Models\Product;

    public function getAvailableProducts(int $supplierId): \Illuminate\Contracts\Pagination\LengthAwarePaginator;

    public function getNearlyOutOfStockProducts(int $supplierId): \Illuminate\Contracts\Pagination\LengthAwarePaginator;

    public function getOutOfStockProducts(int $supplierId): \Illuminate\Contracts\Pagination\LengthAwarePaginator;
}

<?php

namespace App\Services\Contracts;

use App\Models\User;
use App\Models\Product;

interface ProductServiceInterface
{
    public function list(array $filters);
    public function create(array $data, User $user);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function countTotal(): int;
    public function countExpired(): int;
    public function countNearExpiry(): int;
    public function countStockStatuses(): array;
    public function attachMedia(Product $product);
}

<?php

namespace App\Http\Services\Contracts;

use Illuminate\Http\Request;

interface ProductServiceInterface
{
    public function list(array $filters);

    public function create(array $data);
    public function update(int $id, array $data);

    public function delete(int $id);
    public function countTotal(): int;
    public function countExpired(): int;
    public function countNearExpiry(): int;

    public function countStockStatuses(): array;


}

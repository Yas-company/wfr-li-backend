<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\User;

class SupplierSettingService
{
    public function __construct(private Supplier $supplier) {}

    public function updateSupplier(array $data, User $user)
    {

        $supplier = $this->supplier->where('user_id', $user->id)->first();
        $supplier->update($data);

        // Return the user with supplier and fields relationships loaded
        return $user->load(['supplier', 'fields']);
    }
}

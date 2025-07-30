<?php

namespace App\Services\Supplier;

use App\Models\Supplier;
use App\Models\User;

class SupplierSettingService
{
   

    public function updateSupplier(array $data, User $user)
    {

        $supplier = $user->supplier;
        $supplier->update($data);

        // Return the user with supplier and fields relationships loaded
        return $user->load(['supplier', 'fields']);
    }
}

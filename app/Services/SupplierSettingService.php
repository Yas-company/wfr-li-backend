<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\User;
use App\Exceptions\UserException;

class SupplierSettingService
{
   

    public function updateSupplier(array $data, User $user)
    {

        if (!$user->supplier) {
            throw UserException::supplierNotExists();
        }
        $supplier = $user->supplier;
        $supplier->update($data);

        // Return the user with supplier and fields relationships loaded
        return $user->load(['supplier', 'fields']);
    }
}

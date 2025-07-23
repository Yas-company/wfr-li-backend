<?php

namespace App\Services;

class SupplierProfileService
{
    public function updateSupplierProfile($data, $user)
    {
        $user->update($data);

        return $user;
    }
}

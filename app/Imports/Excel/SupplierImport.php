<?php

namespace App\Imports\Excel;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;

class SupplierImport implements ToModel, WithHeadingRow, WithProgressBar
{
    use Importable;

    protected User $createdSupplier;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if(empty($row['name'])) {
            return null;
        }

        $user = new User([
            'name' => $row['name'],
            'phone' => $row['phone'],
            'country_code' => $row['country_code'],
            'business_name' => $row['business_name'],
            'email' => $row['email'],
            'role' => UserRole::SUPPLIER,
            'password' => Hash::make($row['password']),
            'status' => UserStatus::APPROVED,
            'is_verified' => true,
        ]);

        $this->createdSupplier = $user;

        return $user;
    }

    public function createdSupplier(): User
    {
        return $this->createdSupplier;
    }
}

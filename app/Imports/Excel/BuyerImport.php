<?php

namespace App\Imports\Excel;

use App\Models\User;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;

class BuyerImport implements ToModel, WithHeadingRow, WithProgressBar
{
    use Importable;

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

        return new User([
            'name' => $row['name'],
            'phone' => $row['phone'],
            'country_code' => $row['country_code'],
            'business_name' => $row['business_name'],
            'email' => $row['email'],
            'role' => UserRole::BUYER,
            'password' => Hash::make($row['password']),
            'status' => UserStatus::APPROVED,
            'is_verified' => true
        ]);
    }
}

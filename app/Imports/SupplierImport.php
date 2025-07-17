<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;

class SupplierImport implements ToModel, WithHeadingRow, WithProgressBar
{
    use Importable;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new User([
            'id' => $row['id'],
            'name' => $row['name'],
            'phone' => $row['phone'],
            'country_code' => $row['country_code'],
            'business_name' => $row['business_name'],
            'email' => $row['email'],
            'role' => $row['role'],
            'password' => Hash::make($row['password']),
            'status' => $row['status'],
        ]);
    }
}

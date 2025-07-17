<?php

namespace App\Imports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;

class CategoryImport implements ToModel, WithHeadingRow, WithProgressBar
{
    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Category([
            'id' => $row['id'],
            'name' => [
                'ar' => $row['name_ar'],
                'en' => $row['name_en'],
            ],
            'field_id' => $row['field_id'],
            'supplier_id' => $row['supplier_id'],
        ]);
    }
}

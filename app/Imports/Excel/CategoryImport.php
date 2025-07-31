<?php

namespace App\Imports\Excel;

use App\Models\Category;
use App\Models\Field;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;

class CategoryImport implements ToModel, WithHeadingRow, WithProgressBar
{
    use Importable;

    public function __construct(
        protected int $supplierId
    ) {
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if(empty($row['name_en']) || empty($row['name_ar'])) {
            return null;
        }

        $fieldArName = $row['field_name_ar'];
        $fieldEnName = $row['field_name_en'];

        $field = Field::where('name->ar', $fieldArName)->orWhere('name->en', $fieldEnName)->first();

        if(!$field) {
            $field = Field::create([
                'name' => [
                    'ar' => $fieldArName,
                    'en' => $fieldEnName,
                ],
            ]);
        }

        return new Category([
            'name' => [
                'ar' => $row['name_ar'],
                'en' => $row['name_en'],
            ],
            'field_id' => $field->id,
            'supplier_id' => $this->supplierId,
        ]);
    }
}

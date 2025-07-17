<?php

namespace App\Imports;

use App\Models\Field;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;

class FieldImport implements ToModel, WithHeadingRow, WithProgressBar
{
    use Importable;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $imageName = $row['image'];
        $sourcePath = 'images/fields/'.$imageName;
        $destinationPath = 'fields/'.$imageName;

        if (Storage::disk('imports')->exists($sourcePath)) {
            $stream = Storage::disk('imports')->readStream($sourcePath);
            Storage::disk('public')->put($destinationPath, $stream);
        }

        return new Field([
            'id' => $row['id'],
            'name' => [
                'ar' => $row['name_ar'],
                'en' => $row['name_en'],
            ],
            'image' => $destinationPath,
        ]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportedFile extends Model
{
    protected $fillable = ['file_name', 'file_hash', 'imported_at'];
}

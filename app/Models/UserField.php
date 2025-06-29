<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserField extends Model
{
    protected $fillable = ['user_id', 'field_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function field()
    {
        return $this->belongsTo(Field::class);
    }
    
    
}

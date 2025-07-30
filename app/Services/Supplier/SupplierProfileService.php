<?php

namespace App\Services\Supplier;

use Illuminate\Support\Facades\Storage;

class SupplierProfileService
{
    public function updateSupplierProfile($data, $user)
    {
        $user->update($data);

        return $user;
    }

    public function changeSupplierImage($data, $user)
    {
        if ($user->image) {
            Storage::disk('public')->delete($user->image);
        }
        $user->image = $data['image']->store('users', 'public');
        $user->save();

        return $user->load('fields');
    }
}

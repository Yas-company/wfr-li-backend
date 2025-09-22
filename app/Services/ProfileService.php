<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    /**
     * Update buyer profile
     *
     * @param  array  $data
     * @param  User  $user
     * @return User
     */
    public function updateBuyerProfile($data, $user)
    {
        $user->update($data);

        return $user;
    }

    /**
     * Change buyer image
     *
     * @param  array  $data
     * @return User
     */
    public function changeBuyerImage($data, User $user)
    {
        if ($user->image) {
            Storage::disk('public')->delete($user->image);
        }
        $user->image = $data['image']->store('users', 'public');
        $user->save();

        return $user->load('fields');
    }

    /**
     * Update supplier profile
     *
     * @param  array  $data
     * @param  User  $user
     * @return User
     */
    public function updateSupplierProfile($data, $user)
    {
        $user->update($data);

        return $user;
    }

    /**
     * Change supplier image
     *
     * @param  array  $data
     * @return User
     */
    public function changeSupplierImage($data, User $user)
    {
        if ($user->image) {
            Storage::disk('public')->delete($user->image);
        }
        $user->image = $data['image']->store('users', 'public');
        $user->save();

        return $user->load('fields');
    }

    public function deleteAccount(User $user)
    {
        $user->delete();
    }

    public function updatePhone($phone, User $user): void
    {
        $user->phone = $phone;
        $user->save();
    }
}

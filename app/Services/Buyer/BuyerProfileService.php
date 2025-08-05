<?php

namespace App\Services\Buyer;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class BuyerProfileService
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
}

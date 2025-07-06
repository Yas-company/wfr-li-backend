<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\User;
use Illuminate\Auth\Access\Response;


class AddressPolicy
{

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Address $address): Response
    {
        return $user->id === $address->user_id
            ? Response::allow()
            : Response::denyWithStatus(404);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Address $address): Response
    {
        return $user->id === $address->user_id
            ? Response::allow()
            : Response::denyWithStatus(404);
    }
}

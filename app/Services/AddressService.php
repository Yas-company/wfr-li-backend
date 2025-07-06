<?php

namespace App\Services;

use App\Exceptions\UserException;
use App\Models\Address;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AddressService
{
    /**
     * Get the user's addresses.
     *
     * @param User $user
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAddresses(User $user): LengthAwarePaginator
    {
        return $user->addresses()->orderByDesc('is_default')
            ->orderByDesc('created_at')->paginate(10);
    }

    /**
     * Create a new address for the user.
     *
     * @param User $user
     * @param array $data
     *
     * @return Address
     */
    public function createAddress(User $user, array $data): Address
    {
        $data['user_id'] = $user->id;

        $address = Address::create($data);

        if ((bool) $data['is_default']) {
            $user->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        return $address;
    }

    /**
     * Update the specified address.
     *
     * @param User $user
     * @param Address $address
     * @param array $data
     *
     * @return Address
     *
     * @throws UserException
     */
    public function updateAddress(User $user, Address $address, array $data): Address
    {
        $isDefault = (bool) ($data['is_default'] ?? true);

        if ($user->addresses()->count() === 1 && ! $isDefault) {
            throw UserException::atLeastOneDefaultAddressRequired();
        } else {
            $user->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($data);

        return $address;
    }

    /**
     * Delete the specified address.
     *
     * @param User $user
     * @param Address $address
     *
     * @throws UserException
     */
    public function deleteAddress(User $user, Address $address): void
    {
        if ($user->addresses()->count() === 1) {
            throw UserException::cannotDeleteLastAddress();
        }

        if ($address->is_default) {
            $newDefaultAddress = $user->addresses()->where('id', '!=', $address->id)->first();

            if ($newDefaultAddress) {
                $newDefaultAddress->is_default = true;
                $newDefaultAddress->save();
            }
        }
        $address->delete();
    }
}

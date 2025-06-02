<?php

namespace App\Services\Auth;

use App\Enums\UserRole;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class BuyerAuthService
{
    /**
     * Register a new buyer
     *
     * @param array $data
     * @return User
     * @throws ValidationException
     */
    public function register(array $data): User
    {
        // Check if user with this phone number exists (including soft-deleted)
        $existingUser = User::withTrashed()
            ->where('phone', $data['phone'])
            ->first();

        if ($existingUser) {
            if ($existingUser->is_verified && !$existingUser->trashed()) {
                throw ValidationException::withMessages([
                    'phone' => [__('messages.phone_already_registered')],
                ]);
            }

            // If user exists but is soft-deleted, restore it
            if ($existingUser->trashed()) {
                $existingUser->restore();
            }

            // Update existing user
            $existingUser->update([
                'name' => $data['name'],
                'address' => $data['address'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'business_name' => $data['business_name'],
                'lic_id' => $data['lic_id'] ?? null,
                'email' => $data['email'] ?? null,
                'password' => Hash::make($data['password']),
                'role' => UserRole::BUYER,
                'is_verified' => false, // Reset verification status
            ]);

            return $existingUser;
        }

        // Create new user if no existing user found
        return User::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'country_code' => $data['country_code'],
            'address' => $data['address'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'business_name' => $data['business_name'],
            'lic_id' => $data['lic_id'] ?? null,
            'email' => $data['email'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => UserRole::BUYER,
        ]);
    }

    /**
     * Login a buyer
     *
     * @param array $data
     * @return User
     * @throws ValidationException
     */
    public function login(array $data): User
    {
        $user = User::where('phone', $data['phone'])
            ->where('role', UserRole::BUYER)
            ->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'phone' => [__('messages.invalid_credentials')],
            ]);
        }

        if (!$user->is_verified) {
            throw ValidationException::withMessages([
                'phone' => [__('messages.account_not_verified')],
            ]);
        }

        return $user;
    }

    /**
     * Logout a buyer
     *
     * @param User $user
     * @return void
     */
    public function logout(User $user): void
    {
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
    }

    /**
     * Get buyer profile
     *
     * @param User $user
     * @return UserResource
     */
    public function getProfile(User $user): UserResource
    {
        return new UserResource($user);
    }
}
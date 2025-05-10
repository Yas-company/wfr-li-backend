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
     */
    public function register(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'location' => $data['location'],
            'business_name' => $data['business_name'],
            'lic_id' => $data['lic_id'],
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
<?php

namespace App\Http\Controllers\api\v1\Auth;

use App\Enums\UserRole;
use App\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Auth\SupplierLoginRequest;

class SupplierLoginController extends Controller
{
    use ApiResponse;

    /**
     * Handle the incoming request.
     */
    public function __invoke(SupplierLoginRequest $request)
    {
        if(! Auth::attempt([
            'phone' => $request->validated('phone'),
            'password' => $request->validated('password'),
            'role' => UserRole::SUPPLIER->value,
        ])) {
            throw ValidationException::withMessages([
                    'phone' => [__('messages.invalid_credentials')],
            ]);
        }

        $user = Auth::user();

        if(! $user->isVerified()) {
            throw ValidationException::withMessages([
                'phone' => [__('messages.account_not_verified')],
            ]);
        }

        if($user->isSupplier() && ! $user->isApproved()) {
            throw ValidationException::withMessages([
                'phone' => [__('messages.account_pending_approval')],
            ]);
        }

        return $this->successResponse([
                'user' => new UserResource($user),
                'token' => $user->createToken('auth-token')->plainTextToken,
            ], __('messages.login_successful'));
    }
}

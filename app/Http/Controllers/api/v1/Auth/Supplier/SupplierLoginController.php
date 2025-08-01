<?php

namespace App\Http\Controllers\api\v1\Auth\Supplier;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SupplierLoginRequest;
use App\Http\Resources\UserResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SupplierLoginController extends Controller
{
    use ApiResponse;

    /**
     * Supplier Login
     *
     * @param SupplierLoginRequest $request
     *
     * @return JsonResponse $response
     */
    public function __invoke(SupplierLoginRequest $request): JsonResponse
    {
        if (! Auth::attempt([
            'phone' => $request->validated('phone'),
            'password' => $request->validated('password'),
            'role' => UserRole::SUPPLIER->value,
        ])) {
            throw ValidationException::withMessages([
                'phone' => [__('messages.invalid_credentials')],
            ]);
        }

        $user = Auth::user();

        if (! $user->isVerified()) {
            throw ValidationException::withMessages([
                'phone' => [__('messages.account_not_verified')],
            ]);
        }

        if ($user->isSupplier() && ! $user->isApproved()) {
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

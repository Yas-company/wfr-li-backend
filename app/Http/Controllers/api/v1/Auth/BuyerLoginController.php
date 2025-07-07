<?php

namespace App\Http\Controllers\api\v1\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;
use App\Traits\ApiResponse;
use Illuminate\Validation\ValidationException;

class BuyerLoginController extends Controller
{
    use ApiResponse;
    /**
     * Handle the incoming request.
     */
    public function __invoke(LoginRequest $request)
    {
        if(! Auth::attempt([
            'phone' => $request->validated('phone'),
            'password' => $request->validated('password'),
            'role' => UserRole::BUYER->value,
        ])) {
            throw ValidationException::withMessages([
                    'phone' => [__('messages.invalid_credentials')],
            ]);
        }

        $user = Auth::user();

        return $this->successResponse([
                'user' => new UserResource($user),
                'token' => $user->createToken('auth-token')->plainTextToken,
            ], __('messages.login_successful'));
    }
}

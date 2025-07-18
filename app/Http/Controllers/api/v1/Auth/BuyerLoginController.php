<?php

namespace App\Http\Controllers\api\v1\Auth;

use App\Models\User;
use App\Enums\UserRole;
use App\Traits\ApiResponse;
use App\Http\Services\OtpService;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\Auth\BuyerLoginRequest;
use App\Http\Requests\Auth\RequestOtpRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class BuyerLoginController extends Controller
{
    use ApiResponse;

    public function __construct(protected OtpService $otpService)
    {
        //
    }

    public function requestOtp(RequestOtpRequest $request): JsonResponse
    {
        $user = User::role(UserRole::BUYER->value)
                    ->where('phone', $request->validated('phone'))
                    ->first();

        if(!$user) {
            return $this->errorResponse(__('messages.invalid_phone'), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->otpService->generateOtp($request->validated('phone'));

        return $this->successResponse([
            'message' => __('messages.otp_sent'),
        ]);
    }

    public function login(BuyerLoginRequest $request): JsonResponse
    {
        $isValid = $this->otpService->verifyOtp(
            $request->validated('phone'),
            $request->validated('otp')
        );

        if (! $isValid) {
            return $this->errorResponse(
                message: __('messages.invalid_otp'),
                statusCode: Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $user = User::where('phone', $request->validated('phone'))->first();

        return $this->successResponse([
                    'user' => new UserResource($user),
                    'token' => $user->createToken('auth-token')->plainTextToken,
                ], __('messages.login_successful'));
    }
}

<?php

namespace App\Http\Controllers\api\v1\Auth\Buyer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\BuyerLoginRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\OtpService;
use App\Models\User;
use App\Traits\ApiResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BuyerLoginController extends Controller
{
    use ApiResponse;

    /**
     * Buyer Login
     *
     * @param BuyerLoginRequest $request
     * @param OtpService $otpService
     *
     * @return JsonResponse $response
     */
    public function __invoke(BuyerLoginRequest $request, OtpService $otpService): JsonResponse
    {
        $isValid = $otpService->verifyOtp(
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
            'user' => new UserResource($user->load('organizations')),
            'token' => $user->createToken('auth-token')->plainTextToken,
        ], __('messages.login_successful'));
    }
}

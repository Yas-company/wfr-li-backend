<?php

namespace App\Http\Controllers\api\v1\Auth;

use App\Models\User;
use App\Enums\UserRole;
use App\Traits\ApiResponse;
use App\Http\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Requests\Auth\RequestOtpRequest;
use Symfony\Component\HttpFoundation\Response;


class OtpController extends Controller
{
    use ApiResponse;

    /**
     * OtpController constructor.
     *
     * @param OtpService $otpService
     */
    public function __construct(protected OtpService $otpService)
    {
        //
    }


    /**
     * Request Otp
     *
     * @param RequestOtpRequest $request
     *
     * @return JsonResponse
     */
    public function requestOtp(RequestOtpRequest $request): JsonResponse
    {

        $user = User::role(UserRole::BUYER->value)->where('phone', $request->validated('phone'))->first();

        if (!$user) {
            return $this->errorResponse(__('messages.invalid_phone'), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->otpService->generateOtp($user->phone);

        return $this->successResponse(['message' => __('messages.otp_sent')]);
    }

    /**
     * Verify Otp
     *
     * @param VerifyOtpRequest $request
     *
     * @return JsonResponse $response
     */
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        try {
            $isValid = $this->otpService->verifyOtp(
                $request->validated('phone'),
                $request->validated('otp')
            );

            if (! $isValid) {
                return $this->errorResponse(
                    message: __('messages.invalid_otp'),
                    statusCode: 422
                );
            }
            $user = User::where('phone', $request->validated('phone'))->first();

            if ($user && ! $user->is_verified) {
                $user->update(['is_verified' => true]);

                return $this->successResponse([
                    'user' => new UserResource($user),
                    'token' => $user->createToken('auth-token')->plainTextToken,
                ], __('messages.registration_verified'));
            }

            return $this->successResponse(
                message: __('messages.otp_verified')
            );
        } catch (\Exception $e) {
            Log::error('OTP verification failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->errorResponse(
                message: __('messages.otp_verification_failed')
            );
        }
    }
}

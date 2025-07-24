<?php

namespace App\Http\Controllers\api\v1\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RequestOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Requests\RequestOtpAuthRequest;
use App\Http\Requests\VerifyOtpAuthRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\OtpService;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class OtpController extends Controller
{
    use ApiResponse;

    /**
     * OtpController constructor.
     */
    public function __construct(protected OtpService $otpService)
    {
        //
    }

    /**
     * Request Otp
     */
    public function requestOtp(RequestOtpRequest $request): JsonResponse
    {

        $user = User::role(UserRole::BUYER->value)->where('phone', $request->validated('phone'))->first();

        if (! $user) {
            return $this->successResponse([
                'is_registered' => false,
            ], __('messages.otp_send_failed'));
        }

        $this->otpService->generateOtp($user->phone);

        return $this->successResponse([
            'is_registered' => true,
        ], __('messages.otp_sent'));
    }

    /**
     * Verify Otp
     *
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

    /**
     * Request Otp for Auth
     *
     * @param  RequestOtpRequest  $request
     */
    public function requestOtpAuth(RequestOtpAuthRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->otpService->generateOtp($data['phone']);

        return $this->successResponse(
            message: __('messages.otp_sent')
        );
    }

    /**
     * Verify Otp for Auth
     *
     * @param  VerifyOtpRequest  $request
     */
    public function verifyOtpAuth(VerifyOtpAuthRequest $request): JsonResponse
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

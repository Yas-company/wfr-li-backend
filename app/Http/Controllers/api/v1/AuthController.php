<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\OtpService;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly OtpService $otpService
    ) {}

    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return $this->successResponse(
                message: __('messages.logout_successful')
            );
        } catch (\Exception $e) {
            Log::error('Logout failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->serverErrorResponse(__('messages.logout_failed'));
        }
    }

    public function me(Request $request): JsonResponse
    {
        try {
            return $this->successResponse(
                new UserResource($request->user())
            );
        } catch (\Exception $e) {
            Log::error('Failed to get user profile', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->serverErrorResponse(__('messages.profile_failed'));
        }
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (! Hash::check($request->validated('current_password'), $user->password)) {
                return $this->errorResponse(
                    message: __('messages.invalid_current_password'),
                    statusCode: 422
                );
            }

            $user->update([
                'password' => Hash::make($request->validated('password')),
            ]);

            return $this->successResponse(
                message: __('messages.password_changed_successful')
            );
        } catch (\Exception $e) {
            Log::error('Failed to change password', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->errorResponse(
                message: __('messages.password_change_failed')
            );
        }
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $phone = $request->validated('phone');
            $otp = $this->otpService->generateOtp($phone);

            // TODO: Send OTP via SMS service
            Log::info('OTP generated for password reset', [
                'phone' => $phone,
                'otp' => $otp,
            ]);

            return $this->successResponse(
                message: __('messages.otp_sent'),
                data: ['phone' => $phone]
            );
        } catch (\Exception $e) {
            Log::error('Failed to send OTP', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->errorResponse(
                message: __('messages.otp_send_failed')
            );
        }
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $user = User::where('phone', $data['phone'])->first();

            if (! $this->otpService->isVerified($data['phone'])) {
                return $this->errorResponse(
                    message: __('messages.invalid_otp'),
                    statusCode: 422
                );
            }

            $user->update([
                'password' => Hash::make($data['password']),
            ]);

            $this->otpService->clearVerification($data['phone']);

            return $this->successResponse([
                'user' => new UserResource($user),
                'token' => $user->createToken('auth-token')->plainTextToken,
            ], __('messages.password_reset_successful'));
        } catch (\Exception $e) {
            Log::error('Password reset failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->errorResponse(
                message: __('messages.password_reset_failed')
            );
        }
    }

    public function destroy(Request $request): JsonResponse
    {
        try {
            $request->user()->delete();

            return $this->successResponse(
                message: __('messages.account_deleted_successfully')
            );
        } catch (\Exception $e) {
            Log::error('Failed to delete account', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->errorResponse(
                message: __('messages.account_delete_failed')
            );
        }
    }

    public function biometricLogin(Request $request): JsonResponse
    {
        try {
            $token = $request->input('token');

            if (! $token) {
                return $this->errorResponse(
                    message: __('messages.invalid_token'),
                    statusCode: 422
                );
            }

            // Find user by token
            $user = User::whereHas('tokens', function ($query) use ($token) {
                $query->where('token', hash('sha256', $token));
            })->first();

            if (! $user) {
                return $this->errorResponse(
                    message: __('messages.invalid_token'),
                    statusCode: 422
                );
            }

            if (! $user->is_verified) {
                return $this->errorResponse(
                    message: __('messages.account_not_verified'),
                    statusCode: 422
                );
            }

            if ($user->isSupplier() && ! $user->isApproved()) {
                return $this->errorResponse(
                    message: __('messages.account_pending_approval'),
                    statusCode: 422
                );
            }

            // Create new token
            $newToken = $user->createToken('auth-token')->plainTextToken;

            return $this->successResponse([
                'user' => new UserResource($user->load(['organizations'])),
                'token' => $newToken,
            ], __('messages.login_successful'));
        } catch (\Exception $e) {
            Log::error('Biometric login failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->serverErrorResponse(__('messages.login_failed'));
        }
    }
}

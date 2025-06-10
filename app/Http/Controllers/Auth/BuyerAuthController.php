<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\BuyerLoginRequest;
use App\Http\Requests\Auth\BuyerRegisterRequest;
use App\Services\Auth\BuyerAuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Services\OtpService;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Response;
use App\Http\Resources\UserResource;
use Illuminate\Validation\ValidationException;

class BuyerAuthController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly BuyerAuthService $authService,
        private readonly OtpService $otpService
    ) {}

    /**
     * Register a new buyer
     *
     * @param BuyerRegisterRequest $request
     * @return JsonResponse
     */
    public function register(BuyerRegisterRequest $request): JsonResponse
    {
        try {
            $user = $this->authService->register($request->validated());

            
            // Generate and send OTP
            $otp = $this->otpService->generateOtp($user->phone);
            
            // TODO: Send OTP via SMS service
            // For testing, we'll just log it
            Log::info('OTP generated for registration', [
                'phone' => $user->phone,
                'otp' => $otp
            ]);
            
            return $this->createdResponse([
                'user' => new UserResource($user),
                'message' => __('messages.otp_sent'),
                'requires_verification' => true
            ], __('messages.registration_successful'));
        } catch (\Exception $e) {
            Log::error('Buyer registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->serverErrorResponse(__('messages.registration_failed'));
        }
    }

    /**
     * Verify OTP for registration or password reset
     */
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        try {
            $isValid = $this->otpService->verifyOtp(
                $request->validated('phone'),
                $request->validated('otp')
            );

            if (!$isValid) {
                return $this->errorResponse(
                    message: __('messages.invalid_otp'),
                    statusCode: Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            // Check if this is a registration verification
            $user = User::where('phone', $request->validated('phone'))->first();
            if ($user && !$user->is_verified) {
                $user->update(['is_verified' => true]);
                return $this->successResponse([
                    'user' => new UserResource($user),
                    'token' => $user->createToken('buyer-token')->plainTextToken
                ], __('messages.registration_verified'));
            }

            // For password reset, just return success
            return $this->successResponse(
                message: __('messages.otp_verified')
            );
        } catch (\Exception $e) {
            Log::error('OTP verification failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                message: __('messages.otp_verification_failed')
            );
        }
    }

    /**
     * Login a buyer
     *
     * @param BuyerLoginRequest $request
     * @return JsonResponse
     */
    public function login(BuyerLoginRequest $request): JsonResponse
    {
        try {
            $user = $this->authService->login($request->validated());
            
            return $this->successResponse([
                'user' => new UserResource($user),
                'token' => $user->createToken('buyer-token')->plainTextToken
            ], __('messages.login_successful'));
        } catch (ValidationException $e) {
            return $this->errorResponse(
                message: $e->getMessage(),
                errors: $e->errors(),
                statusCode: Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (\Exception $e) {
            Log::error('Buyer login failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->serverErrorResponse(__('messages.login_failed'));
        }
    }

    /**
     * Logout the authenticated buyer
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authService->logout($request->user());
            
            return $this->successResponse(null, __('messages.logout_successful'));
        } catch (\Exception $e) {
            Log::error('Buyer logout failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->serverErrorResponse(__('messages.logout_failed'));
        }
    }

    /**
     * Get the authenticated buyer's profile
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $user = $this->authService->getProfile($request->user());
            
            return $this->successResponse($user);
        } catch (\Exception $e) {
            Log::error('Failed to get buyer profile', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->serverErrorResponse(__('messages.profile_failed'));
        }
    }

    /**
     * Change password when user knows their current password
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (!Hash::check($request->validated('current_password'), $user->password)) {
                return $this->errorResponse(
                    message: __('messages.invalid_current_password'),
                    statusCode: 422
                );
            }
            
            $user->update([
                'password' => Hash::make($request->validated('password'))
            ]);
            
            return $this->successResponse(
                message: __('messages.password_changed_successful')
            );
        } catch (\Exception $e) {
            Log::error('Failed to change password: ' . $e->getMessage(), [
                'user_id' => $request->user()->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse(
                message: __('messages.password_change_failed')
            );
        }
    }

    /**
     * Send OTP for password reset
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $phone = $request->validated('phone');
            $otp = $this->otpService->generateOtp($phone);

            // TODO: Send OTP via SMS service
            // For testing, we'll just log it
            Log::info('OTP generated for password reset', [
                'phone' => $phone,
                'otp' => $otp
            ]);

            return $this->successResponse(
                message: __('messages.otp_sent'),
                data: ['phone' => $phone]
            );
        } catch (\Exception $e) {
            Log::error('Failed to send OTP', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                message: __('messages.otp_send_failed')
            );
        }
    }

    /**
     * Reset password after OTP verification
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $user = User::where('phone', $data['phone'])->first();

            // Check if OTP is verified
            if (!$this->otpService->isVerified($data['phone'])) {
                return $this->errorResponse(
                    message: __('messages.invalid_otp'),
                    statusCode: Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            // Update password
            $user->password = Hash::make($data['password']);
            $user->save();

            // Clear OTP and verification status
            $this->otpService->clearVerification($data['phone']);

            return $this->successResponse([
                'user' => new UserResource($user),
                'token' => $user->createToken('buyer-token')->plainTextToken
            ], __('messages.password_reset_successful'));
        } catch (\Exception $e) {
            Log::error('Password reset failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                message: __('messages.password_reset_failed')
            );
        }
    }

    /**
     * Delete the authenticated buyer's account
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $user->delete();

            return $this->successResponse(
                message: __('messages.account_deleted_successfully')
            );
        } catch (\Exception $e) {
            \Log::error('Failed to delete buyer account', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                message: __('messages.account_delete_failed')
            );
        }
    }
}
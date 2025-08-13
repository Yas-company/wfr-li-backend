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
use OpenApi\Annotations as OA;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Tag(name="Authentication", description="Authentication API")
     */
    public function __construct(
        private readonly OtpService $otpService
    ) {}

    /**
     * Logout current user
     *
     * @OA\Post(
     *     path="/auth/logout",
     *     summary="Logout",
     *     description="Revoke the current access token",
     *     security={{"bearerAuth":{}}},
     *     tags={"Authentication"},
     *     @OA\Response(
     *         response=200,
     *         description="Logged out",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Logged out successfully"),
     *             @OA\Property(property="data", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
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

    /**
     * Get current user profile
     *
     * @OA\Get(
     *     path="/auth/me",
     *     summary="Me",
     *     description="Get the authenticated user's profile",
     *     security={{"bearerAuth":{}}},
     *     tags={"Authentication"},
     *     @OA\Response(
     *         response=200,
     *         description="Profile",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(property="data", ref="#/components/schemas/UserResource")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
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

    /**
     * Change password
     *
     * @OA\Post(
     *     path="/auth/change-password",
     *     summary="Change password",
     *     description="Change the authenticated user's password",
     *     security={{"bearerAuth":{}}},
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(type="object",
     *             required={"current_password","password","password_confirmation"},
     *             @OA\Property(property="current_password", type="string", example="OldPass123"),
     *             @OA\Property(property="password", type="string", example="NewPass123"),
     *             @OA\Property(property="password_confirmation", type="string", example="NewPass123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password changed",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Password changed successfully"),
     *             @OA\Property(property="data", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid current password or validation errors",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
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

    /**
     * Request forgot password OTP
     *
     * @OA\Post(
     *     path="/auth/forgot-password",
     *     summary="Forgot password",
     *     description="Request OTP to reset password",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(type="object",
     *             required={"phone"},
     *             @OA\Property(property="phone", type="string", example="966555555555")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP sent",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="OTP sent"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="phone", type="string", example="966555555555")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
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

    /**
     * Reset password using verified OTP
     *
     * @OA\Post(
     *     path="/auth/reset-password",
     *     summary="Reset password",
     *     description="Reset user password after OTP verification",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(type="object",
     *             required={"phone","password","password_confirmation"},
     *             @OA\Property(property="phone", type="string", example="966555555555"),
     *             @OA\Property(property="password", type="string", example="NewPass123"),
     *             @OA\Property(property="password_confirmation", type="string", example="NewPass123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Password reset successful"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", ref="#/components/schemas/UserResource"),
     *                 @OA\Property(property="token", type="string", example="1|abcdef...")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid OTP or validation error",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid OTP"),
     *             @OA\Property(property="errors", nullable=true)
     *         )
     *     )
     * )
     */
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

    /**
     * Delete current user account
     *
     * @OA\Delete(
     *     path="/auth/delete-account",
     *     summary="Delete account",
     *     description="Delete the authenticated user's account",
     *     security={{"bearerAuth":{}}},
     *     tags={"Authentication"},
     *     @OA\Response(
     *         response=200,
     *         description="Deleted",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Account deleted successfully"),
     *             @OA\Property(property="data", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
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

    /**
     * Login using a biometric token
     *
     * @OA\Post(
     *     path="/auth/biometric-login",
     *     summary="Biometric login",
     *     description="Login using a previously issued biometric token",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(type="object",
     *             required={"token"},
     *             @OA\Property(property="token", type="string", example="1|abcdef...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Logged in",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", ref="#/components/schemas/UserResource"),
     *                 @OA\Property(property="token", type="string", example="1|abcdef...")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid or unverified token",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid token"),
     *             @OA\Property(property="errors", nullable=true)
     *         )
     *     )
     * )
     */
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

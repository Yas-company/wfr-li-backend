<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\OtpService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Enums\UserRole;
use App\Enums\UserStatus;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly OtpService $otpService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            
            // Handle file uploads for suppliers
            if ($data['role'] === UserRole::SUPPLIER->value) {
                if ($request->hasFile('license_attachment')) {
                    $data['license_attachment'] = $request->file('license_attachment')
                        ->store('suppliers/licenses', 'public');
                }
                
                if ($request->hasFile('commercial_register_attachment')) {
                    $data['commercial_register_attachment'] = $request->file('commercial_register_attachment')
                        ->store('suppliers/commercial_registers', 'public');
                }
            }
            
            // Check if user exists (including soft-deleted)
            $existingUser = User::withTrashed()
                ->where('phone', $data['phone'])
                ->first();

            if ($existingUser) {
                // Check if user is a pending supplier
                if ($existingUser->isSupplier() && $existingUser->status === UserStatus::PENDING->value) {
                    return $this->errorResponse(
                        message: __('messages.supplier_pending_review'),
                        statusCode: 422
                    );
                }

                if ($existingUser->is_verified && !$existingUser->trashed()) {
                    throw ValidationException::withMessages([
                        'phone' => [__('messages.phone_already_registered')],
                    ]);
                }

                if ($existingUser->trashed()) {
                    $existingUser->restore();
                }

                $existingUser->update([
                    'name' => $data['name'],
                    'address' => $data['address'],
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'business_name' => $data['business_name'],
                    'email' => $data['email'] ?? null,
                    'password' => Hash::make($data['password']),
                    'role' => $data['role'],
                    'is_verified' => false,
                    'status' => $data['role'] === UserRole::SUPPLIER->value ? UserStatus::PENDING->value : UserStatus::APPROVED->value,
                    'license_attachment' => $data['license_attachment'] ?? null,
                    'commercial_register_attachment' => $data['commercial_register_attachment'] ?? null,
                    'field_id' => $data['field_id'] ?? null,
                ]);

                $user = $existingUser;
            } else {
                $user = User::create([
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'country_code' => $data['country_code'],
                    'address' => $data['address'],
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'business_name' => $data['business_name'],
                    'email' => $data['email'] ?? null,
                    'password' => Hash::make($data['password']),
                    'role' => $data['role'],
                    'status' => $data['role'] === UserRole::SUPPLIER->value ? UserStatus::PENDING->value : UserStatus::APPROVED->value,
                    'license_attachment' => $data['license_attachment'] ?? null,
                    'commercial_register_attachment' => $data['commercial_register_attachment'] ?? null,
                    'field_id' => $data['field_id'] ?? null,
                ]);
            }

            // For buyers, generate and send OTP
            if ($user->isBuyer()) {
                $otp = $this->otpService->generateOtp($user->phone);
                
                // TODO: Send OTP via SMS service
                Log::info('OTP generated for registration', [
                    'phone' => $user->phone,
                    'otp' => $otp
                ]);
                
                return $this->createdResponse([
                    'user' => new UserResource($user),
                    'message' => __('messages.otp_sent'),
                    'requires_verification' => true
                ], __('messages.otp_sent'));
            }

            // For suppliers, return success message
            return $this->createdResponse([
                'user' => new UserResource($user),
                'message' => __('messages.supplier_registration_pending')
            ], __('messages.supplier_registration_pending'));
        } catch (\Exception $e) {
            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->serverErrorResponse(__('messages.registration_failed'));
        }
    }

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
                    statusCode: 422
                );
            }

            $user = User::where('phone', $request->validated('phone'))->first();
            if ($user && !$user->is_verified) {
                $user->update(['is_verified' => true]);
                return $this->successResponse([
                    'user' => new UserResource($user),
                    'token' => $user->createToken('auth-token')->plainTextToken
                ], __('messages.registration_verified'));
            }

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

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $user = User::where('phone', $data['phone'])->first();

            if (!$user || !Hash::check($data['password'], $user->password)) {
                throw ValidationException::withMessages([
                    'phone' => [__('messages.invalid_credentials')],
                ]);
            }

            if (!$user->is_verified) {
                throw ValidationException::withMessages([
                    'phone' => [__('messages.account_not_verified')],
                ]);
            }

            if ($user->isSupplier() && !$user->isApproved()) {
                throw ValidationException::withMessages([
                    'phone' => [__('messages.account_pending_approval')],
                ]);
            }

            return $this->successResponse([
                'user' => new UserResource($user),
                'token' => $user->createToken('auth-token')->plainTextToken
            ], __('messages.login_successful'));
        } catch (ValidationException $e) {
            return $this->errorResponse(
                message: $e->getMessage(),
                errors: $e->errors(),
                statusCode: 422
            );
        } catch (\Exception $e) {
            Log::error('Login failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->serverErrorResponse(__('messages.login_failed'));
        }
    }

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
                'trace' => $e->getTraceAsString()
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
                'trace' => $e->getTraceAsString()
            ]);

            return $this->serverErrorResponse(__('messages.profile_failed'));
        }
    }

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
            Log::error('Failed to change password', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $user = User::where('phone', $data['phone'])->first();

            if (!$this->otpService->isVerified($data['phone'])) {
                return $this->errorResponse(
                    message: __('messages.invalid_otp'),
                    statusCode: 422
                );
            }

            $user->update([
                'password' => Hash::make($data['password'])
            ]);

            $this->otpService->clearVerification($data['phone']);

            return $this->successResponse([
                'user' => new UserResource($user),
                'token' => $user->createToken('auth-token')->plainTextToken
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
                'trace' => $e->getTraceAsString()
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
            
            if (!$token) {
                return $this->errorResponse(
                    message: __('messages.invalid_token'),
                    statusCode: 422
                );
            }

            // Find user by token
            $user = User::whereHas('tokens', function ($query) use ($token) {
                $query->where('token', hash('sha256', $token));
            })->first();

            if (!$user) {
                return $this->errorResponse(
                    message: __('messages.invalid_token'),
                    statusCode: 422
                );
            }

            if (!$user->is_verified) {
                return $this->errorResponse(
                    message: __('messages.account_not_verified'),
                    statusCode: 422
                );
            }

            if ($user->isSupplier() && !$user->isApproved()) {
                return $this->errorResponse(
                    message: __('messages.account_pending_approval'),
                    statusCode: 422
                );
            }

            // Create new token
            $newToken = $user->createToken('auth-token')->plainTextToken;

            return $this->successResponse([
                'user' => new UserResource($user),
                'token' => $newToken
            ], __('messages.login_successful'));
        } catch (\Exception $e) {
            Log::error('Biometric login failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->serverErrorResponse(__('messages.login_failed'));
        }
    }
} 
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
use OpenApi\Annotations as OA;

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
     *
     * @param  RequestOtpRequest  $request
     * @return JsonResponse
     *
     * @OA\Post(
     *     path="/auth/request-otp",
     *     summary="Request OTP",
     *     description="Request OTP",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"phone"},
     *             @OA\Property(property="phone", type="string", example="966555555555"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="OTP sent successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="is_registered", type="boolean", example=true),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found - OTP send failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="OTP send failed"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="is_registered", type="boolean", example=false),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="phone", type="array",
     *                         @OA\Items(type="string", example="The phone field is required."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Internal server error"),
     *         ),
     *     ),
     * )
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
     * @param  VerifyOtpRequest  $request
     * @return JsonResponse $response
     *
     * @OA\Post(
     *     path="/auth/verify-otp",
     *     summary="Verify OTP",
     *     description="Verify OTP",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,   
     *         @OA\JsonContent(
     *             required={"phone", "otp"},
     *             @OA\Property(property="phone", type="string", example="966555555555"),
     *             @OA\Property(property="otp", type="string", example="123456"),
     *         ),
     *     ),
     *     @OA\Response(    
     *         response=200,
     *         description="OTP verified successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="OTP verified successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object", 
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *                     @OA\Property(property="phone", type="string", example="966555555555"),
     *                     @OA\Property(property="token", type="string", example="1|1234567890"),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Registration verified successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Registration verified"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object", 
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *                     @OA\Property(property="phone", type="string", example="966555555555"),
     *                     @OA\Property(property="token", type="string", example="1|1234567890"),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid OTP",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid OTP"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="otp", type="array",
     *                         @OA\Items(type="string", example="The OTP is invalid."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="phone", type="array",
     *                         @OA\Items(type="string", example="The phone field is required."),
     *                     ),
     *                     @OA\Property(property="otp", type="array",
     *                         @OA\Items(type="string", example="The OTP field is required."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="OTP verification failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="OTP verification failed"),
     *         ),
     *     ),
     * )
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
     *
     * @OA\Post(
     *     path="/auth/request-otp-auth",
     *     summary="Request OTP for Authentication",
     *     description="Request OTP for Authentication",
     *     tags={"Authentication"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"phone"},
     *             @OA\Property(property="phone", type="string", example="966555555555"),
     *         ),
     *     ),
     *     @OA\Response(        
     *         response=200,
     *         description="OTP sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="OTP sent successfully"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="phone", type="array",
     *                         @OA\Items(type="string", example="The phone field is required."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Internal server error"),
     *         ),
     *     ),
     * )
     *  
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
     *
     * @OA\Post(
     *     path="/auth/verify-otp-auth",
     *     summary="Verify OTP for Authentication",
     *     description="Verify OTP for Authentication",
     *     tags={"Authentication"}, 
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"phone", "otp"},
     *             @OA\Property(property="phone", type="string", example="966555555555"),
     *             @OA\Property(property="otp", type="string", example="123456"),
     *         ),
     *     ),
     *     @OA\Response(                
     *         response=200,
     *         description="OTP verified successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="OTP verified successfully"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid OTP",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid OTP"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="otp", type="array",
     *                         @OA\Items(type="string", example="The OTP is invalid."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="phone", type="array",
     *                         @OA\Items(type="string", example="The phone field is required."),
     *                     ),
     *                     @OA\Property(property="otp", type="array",
     *                         @OA\Items(type="string", example="The OTP field is required."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="OTP verification failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="OTP verification failed"),
     *         ),
     *     ),
     * )        
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

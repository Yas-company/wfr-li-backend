<?php

namespace App\Http\Controllers\api\v1\Auth\Buyer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\BuyerLoginRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\OtpService;
use App\Models\User;
use App\Traits\ApiResponse;
use OpenApi\Annotations as OA;
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
     *
     * @OA\Post(
     *     path="/auth/buyer/login",
     *     summary="Buyer Login",
     *     description="Buyer Login",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"phone", "otp"},
     *
     *             @OA\Property(property="phone", type="string", example="966555555555"),
     *             @OA\Property(property="otp", type="string", example="123456"),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *                     @OA\Property(property="phone", type="string", example="966555555555"),
     *                     @OA\Property(property="organizations", type="array",
     *
     *                         @OA\Items(type="object",
     *
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="Organization 1"),
     *                             @OA\Property(property="status", type="string", example="active"),
     *                             @OA\Property(property="created_at", type="string", example="2021-01-01 00:00:00"),
     *                             @OA\Property(property="updated_at", type="string", example="2021-01-01 00:00:00"),
     *                         ),
     *                     ),
     *                     @OA\Property(property="token", type="string", example="1|1234567890"),
     *                 ),
     *             ),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Phone number not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Phone number not found"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="phone", type="array",
     *
     *                         @OA\Items(type="string", example="The phone number is not registered in our system."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Invalid OTP",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Invalid OTP"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="otp", type="array",
     *
     *                         @OA\Items(type="string", example="The OTP field is required."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     * )
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

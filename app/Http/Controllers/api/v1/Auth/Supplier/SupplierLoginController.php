<?php

namespace App\Http\Controllers\api\v1\Auth\Supplier;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SupplierLoginRequest;
use App\Http\Resources\UserResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;

class SupplierLoginController extends Controller
{
    use ApiResponse;

    /**
     * Supplier Login
     *
     *
     * @return JsonResponse $response
     *
     * @OA\Post(
     *     path="/auth/supplier/login",
     *     summary="Supplier Login",
     *     description="Supplier Login",
     *     tags={"Authentication"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"phone", "password"},
     *
     *             @OA\Property(property="phone", type="string", example="966555555555"),
     *             @OA\Property(property="password", type="string", example="password"),
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
     *                     @OA\Property(property="token", type="string", example="1|1234567890"),
     *                 ),
     *             ),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Invalid credentials",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Invalid credentials"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="phone", type="array",
     *
     *                         @OA\Items(type="string", example="The phone field is required."),
     *                     ),
     *
     *                     @OA\Property(property="password", type="array",
     *
     *                         @OA\Items(type="string", example="The password field is required."),
     *                     ),
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
     * )
     */
    public function __invoke(SupplierLoginRequest $request): JsonResponse
    {
        if (! Auth::attempt([
            'phone' => $request->validated('phone'),
            'password' => $request->validated('password'),
            'role' => UserRole::SUPPLIER->value,
        ])) {
            throw ValidationException::withMessages([
                'phone' => [__('messages.invalid_credentials')],
            ]);
        }

        $user = Auth::user();

        if (! $user->isVerified()) {
            throw ValidationException::withMessages([
                'phone' => [__('messages.account_not_verified')],
            ]);
        }

        if ($user->isSupplier() && ! $user->isApproved()) {
            throw ValidationException::withMessages([
                'phone' => [__('messages.account_pending_approval')],
            ]);
        }

        return $this->successResponse([
            'user' => new UserResource($user),
            'token' => $user->createToken('auth-token')->plainTextToken,
        ], __('messages.login_successful'));
    }
}

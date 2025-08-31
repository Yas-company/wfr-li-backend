<?php

namespace App\Http\Controllers\api\v1\Auth\Buyer;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\BuyerRegistrationRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\OtpService;
use App\Models\Address;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;

class BuyerRegistrationController extends Controller
{
    use ApiResponse;

    /**
     * Buyer Registration
     *
     *
     * @return JsonResponse $response
     *
     * @OA\Post(
     *     path="/auth/buyer/register",
     *     summary="Buyer Registration",
     *     description="Buyer Registration",
     *     tags={"Authentication"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"name", "phone", "address"},
     *
     *             @OA\Property(property="name", type="string", example="John Doe", description="Buyer's personal name"),
     *             @OA\Property(property="phone", type="string", example="966555555555"),
     *             @OA\Property(property="image", type="string", format="binary", example="image.jpg"),
     *             @OA\Property(property="address", type="object",
     *                 @OA\Property(property="name", type="string", example="Home Address", description="Address name/label"),
     *                 @OA\Property(property="street", type="string", example="123 Main St"),
     *                 @OA\Property(property="city", type="string", example="Riyadh"),
     *                 @OA\Property(property="phone", type="string", example="966555555555"),
     *                 @OA\Property(property="latitude", type="string", example="24.6932"),
     *                 @OA\Property(property="longitude", type="string", example="46.7161"),
     *             ),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Registration successful",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Registration successful"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="phone", type="string", example="966555555555"),
     *                     @OA\Property(property="image", type="string", example="image.jpg"),
     *                     @OA\Property(property="address", type="object",
     *                         @OA\Property(property="name", type="string", example="John Doe"),
     *                         @OA\Property(property="street", type="string", example="123 Main St"),
     *                         @OA\Property(property="city", type="string", example="Riyadh"),
     *                         @OA\Property(property="phone", type="string", example="966555555555"),
     *                         @OA\Property(property="latitude", type="string", example="24.6932"),
     *                         @OA\Property(property="longitude", type="string", example="46.7161"),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="phone", type="array",
     *
     *                         @OA\Items(type="string", example="The phone field is required."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=409,
     *         description="Phone number already registered",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Phone number already registered"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="phone", type="array",
     *
     *                         @OA\Items(type="string", example="The phone number is already registered in our system."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     * )
     */
    public function __invoke(BuyerRegistrationRequest $request, OtpService $otpService): JsonResponse
    {
        $data = $request->validated();

        $existingUser = User::query()
            ->where('phone', $data['phone'])
            ->first();

        if ($existingUser) {
            if ($existingUser->is_verified) {
                throw ValidationException::withMessages([
                    'phone' => [__('messages.phone_already_registered')],
                ]);
            }

            $existingUser->update([
                'name' => $data['name'],
                'role' => UserRole::BUYER->value,
                'is_verified' => false,
                'status' => UserStatus::APPROVED->value,
            ]);

            if ($request->hasFile('image')) {
                if ($existingUser->image) {
                    Storage::disk('public')->delete($existingUser->image);
                }
                $existingUser->image = $request->file('image')->store('users', 'public');
                $existingUser->save();
            }

            $user = $existingUser;
        } else {
            $user = User::create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'role' => UserRole::BUYER->value,
                'status' => UserStatus::APPROVED->value,
            ]);
            if ($request->hasFile('image')) {
                $user->image = $request->file('image')->store('users', 'public');
                $user->save();
            }
        }

        Address::create([
            'name' => $data['address']['name'],
            'street' => $data['address']['street'],
            'city' => $data['address']['city'],
            'phone' => $data['address']['phone'],
            'latitude' => $data['address']['latitude'],
            'longitude' => $data['address']['longitude'],
            'is_default' => true,
            'user_id' => $user->id,
        ]);

        $otp = $otpService->generateOtp($user->phone);

        Log::info('OTP generated for registration', [
            'phone' => $user->phone,
            'otp' => $otp,
        ]);

        return $this->createdResponse([
            'user' => new UserResource($user),
            'message' => __('messages.otp_sent'),
            'requires_verification' => true,
        ], __('messages.otp_sent'));
    }
}

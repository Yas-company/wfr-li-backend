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
use Symfony\Component\HttpFoundation\JsonResponse;

class BuyerRegistrationController extends Controller
{
    use ApiResponse;

    /**
     * Buyer Registration
     *
     * @param BuyerRegistrationRequest $request
     * @param OtpService $otpService
     *
     * @return JsonResponse $response
     */
    public function __invoke(BuyerRegistrationRequest $request, OtpService $otpService): JsonResponse
    {
        $data = $request->validated();

        $existingUser = User::withTrashed()
            ->where('phone', $data['phone'])
            ->first();

        if ($existingUser) {
            if ($existingUser->is_verified && ! $existingUser->trashed()) {
                throw ValidationException::withMessages([
                    'phone' => [__('messages.phone_already_registered')],
                ]);
            }

            if ($existingUser->trashed()) {
                $existingUser->restore();
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

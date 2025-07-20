<?php

namespace App\Http\Controllers\api\v1\Auth\Supplier;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SupplierRegistrationRequest;
use App\Http\Resources\UserResource;
use App\Models\Address;
use App\Models\Supplier;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;

class SupplierRegistrationController extends Controller
{
    use ApiResponse;

    /**
     * Supplier Registration
     *
     * @param SupplierRegistrationRequest $request
     *
     * @return JsonResponse $response
     */
    public function __invoke(SupplierRegistrationRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('license_attachment')) {
                $data['license_attachment'] = $request->file('license_attachment')
                    ->store('suppliers/licenses', 'public');
            }

            if ($request->hasFile('commercial_register_attachment')) {
                $data['commercial_register_attachment'] = $request->file('commercial_register_attachment')
                    ->store('suppliers/commercial_registers', 'public');
            }

            $existingUser = User::withTrashed()
                ->where('phone', $data['phone'])
                ->first();

            if ($existingUser) {
                if ($existingUser->status === UserStatus::PENDING->value) {
                    return $this->errorResponse(
                        message: __('messages.supplier_pending_review'),
                        statusCode: 422
                    );
                }

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
                    'business_name' => $data['business_name'],
                    'email' => $data['email'] ?? null,
                    'password' => Hash::make($data['password']),
                    'role' => UserRole::SUPPLIER->value,
                    'is_verified' => false,
                    'status' => UserStatus::PENDING->value,
                    'license_attachment' => $data['license_attachment'] ?? null,
                    'commercial_register_attachment' => $data['commercial_register_attachment'] ?? null,
                ]);

                foreach ($data['fields'] as $field) {
                    $existingUser->fields()->syncWithoutDetaching($field);
                }

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
                    'country_code' => $data['country_code'] ?? null,
                    'business_name' => $data['business_name'],
                    'email' => $data['email'] ?? null,
                    'password' => Hash::make($data['password']),
                    'role' => UserRole::SUPPLIER->value,
                    'status' => UserStatus::PENDING->value,
                    'license_attachment' => $data['license_attachment'] ?? null,
                    'commercial_register_attachment' => $data['commercial_register_attachment'] ?? null,
                    'image' => $data['image'] ?? null,
                ]);
                foreach ($data['fields'] as $field) {
                    $user->fields()->syncWithoutDetaching($field);
                }
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

            Supplier::createOrFirst([
                'user_id' => $user->id,
                'status' => true,
            ]);

            return $this->createdResponse([
                'user' => new UserResource($user->load('fields')),
                'message' => __('messages.supplier_registration_pending'),
            ], __('messages.supplier_registration_pending'));
        } catch (\Exception $e) {
            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->serverErrorResponse(__('messages.registration_failed'));
        }
    }
}

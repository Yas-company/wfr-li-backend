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
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;

class SupplierRegistrationController extends Controller
{
    use ApiResponse;

    /**
     * Supplier Registration
     *
     *
     * @return JsonResponse $response
     *
     * @OA\Post(
     *     path="/auth/supplier/register",
     *     summary="Supplier Registration",
     *     description="Supplier Registration",
     *     tags={"Authentication"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "phone", "address", "business_name", "password", "fields"},
     *
     *             @OA\Property(property="name", type="string", example="John Doe", description="Supplier's personal name"),
     *             @OA\Property(property="phone", type="string", example="966555555555"),
     *             @OA\Property(property="image", type="string", format="binary", example="image.jpg"),
     *             @OA\Property(property="business_name", type="string", example="ABC Company Ltd", description="Business/company name"),
     *             @OA\Property(property="email", type="string", format="email", example="john@company.com"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="address", type="object",
     *                 @OA\Property(property="name", type="string", example="Main Office", description="Address name/label"),
     *                 @OA\Property(property="street", type="string", example="123 Main St"),
     *                 @OA\Property(property="city", type="string", example="Riyadh"),
     *                 @OA\Property(property="phone", type="string", example="966555555555"),
     *                 @OA\Property(property="latitude", type="string", example="24.6932"),
     *                 @OA\Property(property="longitude", type="string", example="46.7161"),
     *             ),
     *             @OA\Property(property="license_attachment", type="string", format="binary", example="license.jpg"),
     *             @OA\Property(property="commercial_register_attachment", type="string", format="binary", example="commercial_register.jpg"),
     *             @OA\Property(property="fields", type="array",
     *                 @OA\Items(type="integer", example=1),
     *             ),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Registration successful",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Registration successful"),
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
     *
     *     @OA\Response(
     *         response=422,
     *         description="Supplier pending review",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Supplier pending review"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="phone", type="array",
     *
     *                         @OA\Items(type="string", example="Your account is pending review."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     * )
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

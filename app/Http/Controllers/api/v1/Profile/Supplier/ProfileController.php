<?php

namespace App\Http\Controllers\api\v1\Profile\Supplier;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\SupplierImageRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Http\Requests\VerifyOtpAuthRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\OtpService;
use App\Services\ProfileService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    use ApiResponse;

    public function __construct(private ProfileService $profileService) {}

    /**
     * Update supplier profile information
     *
     * @return JsonResponse
     *
     * @OA\Put(
     *     path="/suppliers/profile",
     *     summary="Update supplier profile",
     *     description="Update supplier profile information including name, email, and phone number. Phone number changes require OTP verification.",
     *     tags={"Suppliers"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="name", type="string", example="John Doe", description="Supplier's name"),
     *             @OA\Property(property="email", type="string", format="email", example="supplier@example.com", description="Email address"),
     *             @OA\Property(property="phone", type="string", example="966555555555", description="Phone number (requires OTP verification if changed)"),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Supplier profile updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Supplier profile updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/UserResource")
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="name", type="array",
     *
     *                         @OA\Items(type="string", example="The name field must be a string."),
     *                     ),
     *
     *                     @OA\Property(property="email", type="array",
     *
     *                         @OA\Items(type="string", example="The email must be a valid email address."),
     *                     ),
     *
     *                     @OA\Property(property="phone", type="array",
     *
     *                         @OA\Items(type="string", example="The phone has already been taken."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Not authorized as supplier",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="You are not authorized to update supplier profile"),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=409,
     *         description="Email or phone already exists",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Email or phone already exists"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="email", type="array",
     *
     *                         @OA\Items(type="string", example="The email has already been taken."),
     *                     ),
     *
     *                     @OA\Property(property="phone", type="array",
     *
     *                         @OA\Items(type="string", example="The phone has already been taken."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="OTP verification required for phone number change",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Invalid OTP"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="phone", type="array",
     *
     *                         @OA\Items(type="string", example="Phone number change requires OTP verification."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Internal server error"),
     *         ),
     *     ),
     * )
     */
    public function updateSupplierProfile(UpdateSupplierRequest $request)
    {

        $data = $request->validated();

        $supplier = $this->profileService->updateSupplierProfile($data, Auth::user());

        return $this->successResponse(
            data: new UserResource($supplier),
            message: __('messages.supplier_profile_updated'),
            statusCode: Response::HTTP_OK
        );
    }

    /**
     * Update supplier profile image
     *
     * @return JsonResponse
     *
     * @OA\Post(
     *     path="/suppliers/image",
     *     summary="Update supplier profile image",
     *     description="Upload and update the supplier's profile image",
     *     tags={"Suppliers"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *                 required={"image"},
     *
     *                 @OA\Property(property="image", type="string", format="binary", description="Profile image file (jpeg, png, jpg, gif, svg, max 2MB)"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Supplier profile image updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Supplier image updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/UserResource")
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="image", type="array",
     *
     *                         @OA\Items(type="string", example="The image field is required."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Not authorized as supplier",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="You are not authorized to update supplier profile"),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Invalid image file",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Invalid image file"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="image", type="array",
     *
     *                         @OA\Items(type="string", example="The image must be a file of type: jpeg, png, jpg, gif, svg."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=413,
     *         description="File too large",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="File too large"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="image", type="array",
     *
     *                         @OA\Items(type="string", example="The image must not be greater than 2048 kilobytes."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Internal server error"),
     *         ),
     *     ),
     * )
     */
    public function changeSupplierImage(SupplierImageRequest $request)
    {

        $data = $request->validated();
        $supplier = $this->profileService->changeSupplierImage($data, Auth::user());

        return $this->successResponse(
            data: new UserResource($supplier),
            message: __('messages.suppliers.image_changed'),
            statusCode: Response::HTTP_OK
        );
    }

    public function destroy(): JsonResponse
    {
        try {
            $this->profileService->deleteAccount(Auth::user());

            return $this->successResponse(
                message: __('messages.profile.deleted'),
                statusCode: Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to delete account', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->errorResponse(
                message: __('messages.profile.delete_failed'),
                statusCode: Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }

    public function updateSupplierPhone(VerifyOtpAuthRequest $request, OtpService $otpService)
    {
        try {
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

            $this->profileService->updatePhone($request->validated('phone'), Auth::user());

            return $this->successResponse(
                message: __('messages.profile.phone_updated'),
                statusCode: Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('OTP verification failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->errorResponse(
                message: __('messages.profile.phone_update_failed')
            );
        }

    }
}

<?php

namespace App\Http\Controllers\api\v1;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Services\OtpService;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Services\SupplierProfileService;
use App\Http\Requests\UpdateSupplierRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Http\Requests\Supplier\SupplierImageRequest;
use OpenApi\Annotations as OA;

class SupplierProfileController extends Controller
{
    use ApiResponse;

    public function __construct(private SupplierProfileService $supplierProfileService) {}

    /**
     * Update supplier profile information
     *
     * @param  UpdateSupplierRequest  $request
     * @param  OtpService  $otpService
     * @return JsonResponse
     * 
     * @OA\Put(
     *     path="/suppliers/profile",
     *     summary="Update supplier profile",
     *     description="Update supplier profile information including name, email, and phone number. Phone number changes require OTP verification.",
     *     tags={"Suppliers"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe", description="Supplier's name"),
     *             @OA\Property(property="email", type="string", format="email", example="supplier@example.com", description="Email address"),
     *             @OA\Property(property="phone", type="string", example="966555555555", description="Phone number (requires OTP verification if changed)"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Supplier profile updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Supplier profile updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/UserResource")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="name", type="array",
     *                         @OA\Items(type="string", example="The name field must be a string."),
     *                     ),
     *                     @OA\Property(property="email", type="array",
     *                         @OA\Items(type="string", example="The email must be a valid email address."),
     *                     ),
     *                     @OA\Property(property="phone", type="array",
     *                         @OA\Items(type="string", example="The phone has already been taken."),
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
     *         response=403,
     *         description="Forbidden - Not authorized as supplier",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You are not authorized to update supplier profile"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Email or phone already exists",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Email or phone already exists"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="email", type="array",
     *                         @OA\Items(type="string", example="The email has already been taken."),
     *                     ),
     *                     @OA\Property(property="phone", type="array",
     *                         @OA\Items(type="string", example="The phone has already been taken."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="OTP verification required for phone number change",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid OTP"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="phone", type="array",
     *                         @OA\Items(type="string", example="Phone number change requires OTP verification."),
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
    public function updateSupplierProfile(UpdateSupplierRequest $request, OtpService $otpService)
    {

        $data = $request->validated();
        if (isset($data['phone']) && $data['phone'] !== Auth::user()->phone) {

            $isValid = $otpService->isVerified($data['phone']);

            if (! $isValid) {
                return $this->errorResponse(
                    message: __('messages.invalid_otp'),
                    statusCode: Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }
            $data['is_verified'] = true;
        }

        $supplier = $this->supplierProfileService->updateSupplierProfile($data, Auth::user());

        return $this->successResponse(
            data: new UserResource($supplier),
            message: __('messages.supplier_profile_updated'),
            statusCode: Response::HTTP_OK
        );
    }

    /**
     * Update supplier profile image
     *
     * @param  SupplierImageRequest  $request
     * @return JsonResponse
     * 
     * @OA\Post(
     *     path="/suppliers/image",
     *     summary="Update supplier profile image",
     *     description="Upload and update the supplier's profile image",
     *     tags={"Suppliers"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"image"},
     *                 @OA\Property(property="image", type="string", format="binary", description="Profile image file (jpeg, png, jpg, gif, svg, max 2MB)"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Supplier profile image updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Supplier image updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/UserResource")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="image", type="array",
     *                         @OA\Items(type="string", example="The image field is required."),
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
     *         response=403,
     *         description="Forbidden - Not authorized as supplier",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You are not authorized to update supplier profile"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid image file",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid image file"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="image", type="array",
     *                         @OA\Items(type="string", example="The image must be a file of type: jpeg, png, jpg, gif, svg."),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=413,
     *         description="File too large",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="File too large"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="image", type="array",
     *                         @OA\Items(type="string", example="The image must not be greater than 2048 kilobytes."),
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
    public function changeSupplierImage(SupplierImageRequest $request)
    {

        $data = $request->validated();
        $supplier = $this->supplierProfileService->changeSupplierImage($data, Auth::user());

        return $this->successResponse(
            data: new UserResource($supplier),
            message: __('messages.suppliers.image_changed'),
            statusCode: Response::HTTP_OK
        );
    }
}

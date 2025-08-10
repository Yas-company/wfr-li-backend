<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierSettingRequest;
use App\Http\Resources\SupplierResource;
use App\Services\SupplierSettingService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Supplier",
 *     description="Supplier endpoints"
 * )
 */
class SupplierSettingController extends Controller
{
    use ApiResponse;

    /**
     * Summary of __construct
     */
    public function __construct(private SupplierSettingService $supplierSettingService) {}

    /**
     * Summary of update
     *
     * @return \Illuminate\Http\JsonResponse
     * 
     * @OA\Put(
     *     path="/suppliers/setting",
     *     summary="Update supplier settings",
     *     description="Update supplier settings",
     *     tags={"Suppliers"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Supplier settings updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Supplier settings updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/SupplierResource")
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
     *             @OA\Property(property="message", type="string", example="You are not authorized to update supplier settings"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="errors", type="object",
     *                     @OA\Property(property="status", type="array",
     *                         @OA\Items(type="string", example="The status field is required.")
     *                     )
     *                 )
     *             )
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
    public function update(SupplierSettingRequest $request)
    {
        $result = $this->supplierSettingService->updateSupplier($request->validated(), Auth::user());

        return $this->successResponse(new SupplierResource($result), __('messages.supplier_setting_updated'));
    }
}

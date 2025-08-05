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

class SupplierProfileController extends Controller
{
    use ApiResponse;

    public function __construct(private SupplierProfileService $supplierProfileService) {}

    /**
     * Update supplier profile
     *
     * @param  Request  $request
     * @return JsonResponse $response
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
     * Change supplier image
     *
     * @param  Request  $request
     * @return JsonResponse $response
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

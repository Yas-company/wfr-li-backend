<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\SupplierImageRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Http\Resources\Supplier\SupplierUpdatedResource;
use App\Http\Services\OtpService;
use App\Services\SupplierProfileService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

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
            data: new SupplierUpdatedResource($supplier),
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
            data: new SupplierUpdatedResource($supplier),
            message: __('messages.suppliers.image_changed'),
            statusCode: Response::HTTP_OK
        );
    }
}

<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierSettingRequest;
use App\Http\Resources\SupplierResource;
use App\Services\SupplierSettingService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;

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
     */
    public function update(SupplierSettingRequest $request)
    {
        $result = $this->supplierSettingService->updateSupplier($request->all(), Auth::user());

        return $this->successResponse(new SupplierResource($result), __('messages.supplier_setting_updated'));
    }
}

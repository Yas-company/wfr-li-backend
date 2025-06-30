<?php

namespace App\Http\Controllers\api\v1;


use App\Http\Controllers\Controller;
use App\Http\Requests\SearchSupplierRequest;
use App\Http\Resources\FieldResource;
use App\Http\Resources\SupplierDetailsResource;
use App\Http\Resources\SupplierResource;
use App\Http\Resources\UserResource;
use App\Http\Services\UserService;
use App\Models\User;
use App\Traits\ApiResponse;

class UserController extends Controller
{
    use ApiResponse;
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Return a list of all suppliers (any status).
     */
    public function suppliers()
    {
        $result = $this->userService->suppliers();
        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], 500);
        }

        return $this->paginatedResponse($result, SupplierResource::collection($result),'Suppliers retrieved successfully',statusCode: 200);
    }

    public function show(int $user_id)
    {
        $result = $this->userService->show($user_id);
        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], 500);
        }
   
        return $this->successResponse(new SupplierDetailsResource($result),'Supplier retrieved successfully',200);
    }

    public function searchSuppliers(SearchSupplierRequest $request)
    {
        $result = $this->userService->searchSuppliers($request);
        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], 500);
        }
        return $this->paginatedResponse($result, SupplierResource::collection($result),'Suppliers retrieved successfully',statusCode: 200);
    }

    public function getSupplierFields()
    {
        $result = $this->userService->getSupplierFields();
        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], 500);
        }
        return $this->successResponse(FieldResource::collection($result),'Fields retrieved successfully',200);

    }
}

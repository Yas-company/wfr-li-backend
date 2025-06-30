<?php

namespace App\Http\Controllers\api\v1;


use App\Http\Controllers\Controller;
use App\Http\Resources\FieldResource;
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

        // $suppliers = User::where('role', UserRole::SUPPLIER)
        // ->where('status', UserStatus::APPROVED)->get();
        return $this->successResponse(UserResource::collection($result),'Suppliers retrieved successfully',statusCode: 200);
    }

    public function show(User $user)
    {
        $result = $this->userService->show($user);
        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], 500);
        }
        // if (!$user || $user->role !== UserRole::SUPPLIER || $user->status !== UserStatus::APPROVED) {
        //     return $this->errorResponse('Supplier not found');
        // }
        // $user->load('categories');
        return $this->successResponse(new UserResource($result),'Supplier retrieved successfully',200);
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

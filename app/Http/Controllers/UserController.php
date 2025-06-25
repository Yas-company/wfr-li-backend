<?php

namespace App\Http\Controllers;


use App\Http\Resources\UserResource;
use App\Traits\ApiResponse;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;

class UserController extends Controller
{
    use ApiResponse;

    /**
     * Return a list of all suppliers (any status).
     */
    public function suppliers()
    {
        $suppliers = User::where('role', UserRole::SUPPLIER)
        ->where('status', UserStatus::APPROVED)->get();
        return $this->successResponse(UserResource::collection($suppliers));
    }

    public function show(User $user)
    {
        if (!$user || $user->role !== UserRole::SUPPLIER || $user->status !== UserStatus::APPROVED) {
            return $this->errorResponse('Supplier not found');
        }
        $user->load('categories');
        return $this->successResponse(new UserResource($user));
    }
}

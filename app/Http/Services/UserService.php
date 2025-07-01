<?php

namespace App\Http\Services;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use App\Models\UserField;
use Illuminate\Support\Facades\Auth;

class UserService
{
public function getSupplierFields()
    {
        $user = Auth::user();
        $fields = UserField::where('user_id', $user->id)->with('field')->get();
        return $fields;
    }

    public function suppliers()
    {
    $suppliers = User::where('role', UserRole::SUPPLIER)
            ->where('status', UserStatus::APPROVED)
            ->with('fields')
            ->paginate(10);
        return $suppliers;
    }

    public function show(int $user_id)
    {
        $user = User::find($user_id);
        if (!$user || $user->role !== UserRole::SUPPLIER || $user->status !== UserStatus::APPROVED) {
            return ['error' => 'Supplier not found'];
        }
    $user->load('categories')->with('fields');
        return $user;
    }
    public function searchSuppliers($request)
    {
        $search = $request->search;
        $suppliers = User::where('name', 'like', '%' . $search . '%')
            ->where('role', UserRole::SUPPLIER)
            ->where('status', UserStatus::APPROVED)
            ->with('fields')
            ->paginate(10);
        return $suppliers;
    }

}

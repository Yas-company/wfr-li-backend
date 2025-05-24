<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SupplierAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $supplier = Supplier::where('email', $request->email)->first();

        if (!$supplier || !Hash::check($request->password, $supplier->password)) {
            throw ValidationException::withMessages([
                'email' => [__('messages.invalid_credentials')],
            ]);
        }

        if (!$supplier->is_verified) {
            throw ValidationException::withMessages([
                'email' => [__('messages.account_not_verified')],
            ]);
        }

        $token = $supplier->createToken('supplier-token')->plainTextToken;

        return response()->json([
            'supplier' => $supplier,
            'token' => $token,
            'message' => __('messages.login_successful')
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'message' => __('messages.logout_successful')
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'supplier' => $request->user(),
            'message' => __('messages.success')
        ]);
    }
} 
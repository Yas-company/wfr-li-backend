<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Traits\ApiResponse;

class PaymentMethodController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $methods = PaymentMethod::where('is_active', true)->get(['id', 'name_ar', 'name_en', 'code']);
        return $this->successResponse($methods, 'طرق الدفع المتاحة');
    }
}

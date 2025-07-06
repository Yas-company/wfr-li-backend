<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\InterestSubmission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class InterestController extends Controller
{
    /**
     * Store interest form submission
     */
    public function store(Request $request): JsonResponse
    {
        // Add CORS headers for production
        if ($request->isMethod('options')) {
            return response()->json([], 200, [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'POST, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, Accept, Authorization',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'business_type' => 'required|string|in:restaurant,cafe,grocery,supermarket,catering,other',
            'city' => 'required|string|in:makkah,jeddah,riyadh,dammam,medina,other',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'البيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422, [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'POST, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, Accept, Authorization',
            ]);
        }

        try {
            // Store the interest data
            $interest = InterestSubmission::create([
                'name' => $request->name,
                'email' => $request->email,
                'business_type' => $request->business_type,
                'city' => $request->city,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل اهتمامك بنجاح! سنتواصل معك قريباً.',
                'data' => [
                    'id' => $interest->id,
                    'name' => $interest->name,
                    'email' => $interest->email,
                ]
            ], 201, [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'POST, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, Accept, Authorization',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تسجيل اهتمامك، يرجى المحاولة مرة أخرى.',
                'error' => $e->getMessage()
            ], 500, [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'POST, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, Accept, Authorization',
            ]);
        }
    }
} 
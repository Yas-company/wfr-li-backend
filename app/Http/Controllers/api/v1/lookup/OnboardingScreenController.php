<?php

namespace App\Http\Controllers\api\v1\lookup;

use App\Http\Controllers\Controller;
use App\Http\Resources\OnboardingScreenResource;
use App\Models\OnboardingScreen;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class OnboardingScreenController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the onboarding screens.
     *
     * @return JsonResponse
     * 
     * @OA\Get(
     *     path="/onboarding-screens",
     *     summary="Get all active onboarding screens",
     *     description="Get all active onboarding screens ordered by their display order",
     *     tags={"Onboarding Screens"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Onboarding screens retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Onboarding screens retrieved successfully"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="object",
     *                         @OA\Property(property="en", type="string", example="Welcome to Our App"),
     *                         @OA\Property(property="ar", type="string", example="مرحباً بك في تطبيقنا"),
     *                     ),
     *                     @OA\Property(property="description", type="object",
     *                         @OA\Property(property="en", type="string", example="Discover amazing features"),
     *                         @OA\Property(property="ar", type="string", example="اكتشف الميزات الرائعة"),
     *                     ),
     *                     @OA\Property(property="image", type="string", example="https://example.com/onboarding1.jpg"),
     *                     @OA\Property(property="order", type="integer", example=1),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="created_at", type="string", example="2021-01-01 00:00:00"),
     *                     @OA\Property(property="updated_at", type="string", example="2021-01-01 00:00:00"),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Not authorized to access onboarding screens",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You are not authorized to access onboarding screens"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No active onboarding screens found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No active onboarding screens found"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Internal server error"),
     *         ),
     *     ),
     * )
     */
    public function index(): JsonResponse
    {
        $screens = OnboardingScreen::active()
            ->orderBy('order')
            ->get();

        return $this->successResponse(
            OnboardingScreenResource::collection($screens)
        );
    }
}

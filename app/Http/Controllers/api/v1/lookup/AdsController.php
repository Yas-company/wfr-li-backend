<?php

namespace App\Http\Controllers\api\v1\lookup;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdsResource;
use App\Models\Ads;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class AdsController extends Controller
{
    use ApiResponse;

    /**
     * Get all active ads
     *
     * @return JsonResponse
     * 
     * @OA\Get(
     *     path="/ads",
     *     summary="Get all active ads",
     *     description="Get all active ads",
     *     tags={"Ads"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Ads retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ads retrieved successfully"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Ad Title"),
     *                     @OA\Property(property="description", type="string", example="Ad Description"),   
     *                     @OA\Property(property="image", type="string", example="https://example.com/image.jpg"),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="user", type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="John Doe"),
     *                         @OA\Property(property="email", type="string", example="john@example.com"),
     *                     ),
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
     *         description="Forbidden - Not authorized to access ads",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You are not authorized to access ads"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No active ads found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No active ads found"),
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
        $ads = Ads::with('user')
            ->where('is_active', true)
            ->latest()
            ->get();

    return $this->successResponse(AdsResource::collection($ads), __('messages.ads.retrieved_successfully'));
    }
}

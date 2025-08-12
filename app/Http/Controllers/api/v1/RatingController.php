<?php

namespace App\Http\Controllers\api\v1;

use App\Models\Rating;
use App\Traits\ApiResponse;
use App\Services\RatingService;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Rating\StoreRatingRequest;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
class RatingController extends Controller
{
    use ApiResponse;

    public function __construct(protected RatingService $ratingService)
    {
        //
    }

    /**
     * Store a rating.
     *
     * @param StoreRatingRequest $request
     *
     * @return JsonResponse
     *
     * @OA\Post(
     *     path="/ratings",
     *     summary="Store a rating",
     *     tags={"Ratings"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="rateable_id", type="integer", example=1),
     *             @OA\Property(property="rateable_type", type="string", example="product"),
     *             @OA\Property(property="rating", type="integer", example=5),
     *             @OA\Property(property="comment", type="string", example="Great product!")
     *         )    
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Rating created successfully"
     *     ),
     *     @OA\Response(
     *         response=401,    
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )                    
     * )
     */
    public function store(StoreRatingRequest $request): JsonResponse
    {
        $data = $request->validated();

        $this->authorize('create', [Rating::class, $data['rateable_type'], $data['rateable_id']]);

        $this->ratingService->rate(
            auth()->id(),
            $data['rateable_id'],
            $data['rateable_type'],
            $data['rating'],
            $data['comment']
        );

        return $this->successResponse(
            message: __('messages.ratings.rated_successfully'),
            statusCode: Response::HTTP_CREATED,
        );
    }
}

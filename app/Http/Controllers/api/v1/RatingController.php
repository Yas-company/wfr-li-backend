<?php

namespace App\Http\Controllers\api\v1;

use App\Models\Rating;
use App\Traits\ApiResponse;
use App\Services\RatingService;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Rating\StoreRatingRequest;
use Illuminate\Http\JsonResponse;

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

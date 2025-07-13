<?php

namespace App\Services;

use App\Models\Rating;

class RatingService
{
    /**
     * Rate a rateable.
     *
     * @param int $userId
     * @param int $rateableId
     * @param string $rateableType
     * @param int $rating
     * @param string|null $comment
     *
     */
    public function rate(int $userId, int $rateableId, string $rateableType, int $rating, ?string $comment = null): void
    {
        Rating::updateOrCreate([
            'user_id' => $userId,
            'rateable_id' => $rateableId,
            'rateable_type' => $rateableType,
        ], [
            'rating' => $rating,
            'comment' => $comment,
        ]);
    }
}

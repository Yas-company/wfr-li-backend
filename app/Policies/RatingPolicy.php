<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\Morphs\RatingModel;
use Illuminate\Database\Eloquent\Relations\Relation;

class RatingPolicy
{

    public function create(User $user, string $rateableType, int $rateableId): bool
    {
        if (!in_array($rateableType, RatingModel::getMorphClasses()))
        {
            return false;
        }

        $className = Relation::getMorphedModel($rateableType);

        $model = forward_static_call([$className, 'find'], $rateableId);

        if (!$model)
        {
            return false;
        }

        if ($rateableType === RatingModel::ORDER->value)
        {
            return $model->user_id === $user->id;
        }

        return true;
    }
}

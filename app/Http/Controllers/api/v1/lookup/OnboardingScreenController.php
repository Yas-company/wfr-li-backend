<?php

namespace App\Http\Controllers\api\v1\lookup;

use App\Http\Controllers\Controller;
use App\Http\Resources\OnboardingScreenResource;
use App\Models\OnboardingScreen;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class OnboardingScreenController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the onboarding screens.
     *
     * @return JsonResponse
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

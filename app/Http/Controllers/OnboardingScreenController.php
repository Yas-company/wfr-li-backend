<?php

namespace App\Http\Controllers;

use App\Models\OnboardingScreen;
use Illuminate\Http\JsonResponse;
use App\Traits\ApiResponse;
use App\Http\Resources\OnboardingScreenResource;

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

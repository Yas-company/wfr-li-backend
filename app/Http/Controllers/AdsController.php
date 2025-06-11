<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdsResource;
use App\Models\Ads;

class AdsController extends Controller
{
    /**
     * Get all active ads
     */
    public function index()
    {
        $ads = Ads::with('supplier')
            ->where('is_active', true)
            ->latest()
            ->get();

        return  AdsResource::collection($ads);
    }
}

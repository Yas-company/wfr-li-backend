<?php

use App\Http\Controllers\api\v1\InterestController;
use Illuminate\Support\Facades\Route;

// Public routes (no CSRF protection needed)
Route::options('/interest', function () {
    return response()->json([], 200, [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'POST, OPTIONS',
        'Access-Control-Allow-Headers' => 'Content-Type, Accept, Authorization',
    ]);
});
Route::post('/interest', [InterestController::class, 'store'])->name('api.interest.store');
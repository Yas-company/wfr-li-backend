<?php

use App\Http\Controllers\api\v1\lookup\AdsController;
use App\Http\Controllers\api\v1\lookup\FieldController;
use App\Http\Controllers\api\v1\lookup\OnboardingScreenController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'guest'], function () {
    //categories
    Route::get('/onboarding-screens', [OnboardingScreenController::class, 'index']);
    Route::get('/ads', [AdsController::class, 'index']);
    Route::get('/fields', [FieldController::class, 'index']);
    Route::get('/fields/{field}', [FieldController::class, 'show']);


});

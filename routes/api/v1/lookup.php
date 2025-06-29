<?php

use App\Http\Controllers\api\v1\lookup\AdsController;
use App\Http\Controllers\api\v1\lookup\FieldController;
use App\Http\Controllers\api\v1\lookup\OnboardingScreenController;
use App\Http\Controllers\api\v1\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:sanctum'], function () {
    //categories
    Route::get('/onboarding-screens', [OnboardingScreenController::class, 'index']);
    Route::get('/ads', [AdsController::class, 'index']);
    Route::get('/fields/supplier', [UserController::class, 'getSupplierFields']);
    Route::get('/fields', [FieldController::class, 'index']);
    Route::get('/fields/{field}', [FieldController::class, 'show']);


});

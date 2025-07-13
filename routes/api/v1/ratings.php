<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\v1\RatingController;

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/ratings', [RatingController::class, 'store'])->name('ratings.store');
});

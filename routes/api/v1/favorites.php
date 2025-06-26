<?php

use App\Http\Controllers\api\v1\FavoritesController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'guest'], function () {
    // Favorites Routes
    Route::get('/favorites', [FavoritesController::class, 'index']);
    Route::post('/favorites', [FavoritesController::class, 'store']);
    Route::delete('/favorites/{product}', [FavoritesController::class, 'destroy']);
});

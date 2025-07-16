<?php

use App\Http\Controllers\api\v1\FavoriteController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('favorite')->group(function () {
    Route::post('/toggle', [FavoriteController::class, 'toggleFavorite']);
    Route::get('/', [FavoriteController::class, 'index']);
});
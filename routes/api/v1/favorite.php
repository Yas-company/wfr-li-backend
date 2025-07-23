<?php

use App\Http\Controllers\api\v1\FavoriteController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('favorite')->group(function () {
    Route::post('/toggle', [FavoriteController::class, 'toggleFavorite'])->name('favorite.toggle');
    Route::get('/', [FavoriteController::class, 'index'])->name('favorite.index');
});

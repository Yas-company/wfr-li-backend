<?php

use App\Http\Controllers\api\v1\CategoryController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:sanctum'], function () {
    //categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories/search', [CategoryController::class, 'search']);
    Route::get('/categories/field/{field_id}', [CategoryController::class, 'getCategoriesByField']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::post('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
});

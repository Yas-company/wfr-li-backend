<?php

use App\Enums\UserRole;
use App\Http\Controllers\api\v1\HomeController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('buyer')->middleware(RoleMiddleware::class.':'.UserRole::BUYER->value)->group(function () {
        Route::get('/suppliers-and-products', [HomeController::class, 'getSuppliersAndProducts'])->name('home.suppliers-and-products');
    });
});

<?php

use App\Http\Controllers\api\v1\Buyer\BuyerHomeController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;
use App\Enums\UserRole;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('buyer')->middleware(RoleMiddleware::class.':'.UserRole::BUYER->value)->group(function () {
        Route::get('/suppliers-and-products', [BuyerHomeController::class, 'getSuppliersAndProducts'])->name('home.suppliers-and-products');
    });
});
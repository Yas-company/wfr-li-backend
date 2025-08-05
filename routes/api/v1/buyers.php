<?php

use App\Enums\UserRole;
use App\Http\Controllers\api\v1\Buyer\BuyerProfileController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', RoleMiddleware::class.':'.UserRole::BUYER->value])->group(function () {
    Route::put('/buyers/profile', [BuyerProfileController::class, 'updateBuyerProfile'])->name('buyers.profile.update');
    Route::post('/buyers/image', [BuyerProfileController::class, 'changeBuyerImage'])->name('buyers.image.change');
});

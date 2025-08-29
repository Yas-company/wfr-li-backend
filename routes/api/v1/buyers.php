<?php

use App\Enums\UserRole;
use App\Http\Controllers\api\v1\Profile\Buyer\ProfileController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', RoleMiddleware::class.':'.UserRole::BUYER->value])->group(function () {
    Route::put('/buyers/profile', [ProfileController::class, 'updateBuyerProfile'])->name('buyers.profile.update');
    Route::post('/buyers/image', [ProfileController::class, 'changeBuyerImage'])->name('buyers.image.change');
    Route::delete('/buyers/profile', [ProfileController::class, 'destroy'])->name('buyers.profile.delete');
});

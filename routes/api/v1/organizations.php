<?php

use App\Enums\UserRole;
use App\Http\Controllers\api\v1\Organization\Buyer\OrganizationController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', RoleMiddleware::class.':'.UserRole::BUYER->value])->prefix('buyer/organizations')->group(function () {
    Route::post('/', [OrganizationController::class, 'store'])->name('buyer.organizations.store');
    Route::get('/check', [OrganizationController::class, 'checkOrganization'])->name('buyer.organizations.check');
});

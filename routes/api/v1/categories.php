<?php

use App\Enums\UserRole;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\api\v1\Category\CategoryController;
use App\Http\Controllers\api\v1\Category\Supplier\GetAllCategoriesController;

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

    Route::middleware(RoleMiddleware::class.':'.UserRole::SUPPLIER->value)->prefix('supplier')->group(function () {
        Route::get('/categories', GetAllCategoriesController::class)->name('supplier.categories.index');
    });
});



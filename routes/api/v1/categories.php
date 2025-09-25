<?php

use App\Enums\UserRole;
use App\Http\Controllers\api\v1\Category\CategoryController;
use App\Http\Controllers\api\v1\Category\Supplier\GetAllCategoriesController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

    Route::middleware(RoleMiddleware::class.':'.UserRole::SUPPLIER->value)->prefix('supplier')->group(function () {
        Route::get('/categories', GetAllCategoriesController::class)->name('supplier.categories.index');
        Route::get('/supplier-categories', [CategoryController::class, 'getSupplierCategories'])->name('supplier.categories.supplier');
    });
});

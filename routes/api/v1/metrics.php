<?php

use App\Enums\UserRole;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\api\v1\Metrics\SupplierGeneralMetricsController;

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::prefix('supplier')->middleware(RoleMiddleware::class . ':' . UserRole::SUPPLIER->value)->group(function () {
        Route::get('/general-metrics', [SupplierGeneralMetricsController::class, 'getMetrics'])->name('metrics.supplier.general-metrics');
    });
});

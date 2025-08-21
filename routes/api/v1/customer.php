<?php

use App\Http\Controllers\api\v1\SupplierController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('supplier')
    ->controller(SupplierController::class)->group(function () {
    Route::get('{supplierId}/ads', 'ads');
    Route::get('{supplierId}/products', 'products');
    Route::get('products/{id}','showProduct');
});

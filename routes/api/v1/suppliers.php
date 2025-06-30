<?php

use App\Http\Controllers\api\v1\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'guest'], function () {
    //suppliers
    Route::get('/suppliers', [UserController::class, 'suppliers']);
    Route::post('/suppliers/search', [UserController::class, 'searchSuppliers']);
    Route::get('/suppliers/{user}', [UserController::class, 'show']);
});

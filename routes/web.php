<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index']);
Route::get('/category/{category}/products', [HomeController::class, 'categoryProducts'])->name('category.products');

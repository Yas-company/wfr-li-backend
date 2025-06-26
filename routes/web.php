<?php

use App\Http\Controllers\api\v1\HomeController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/category/{category}/products', [HomeController::class, 'categoryProducts'])->name('category.products');
Route::get('/pages/{slug}', [HomeController::class, 'page'])->name('page.show');

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/category/{category}/products', [HomeController::class, 'categoryProducts'])->name('category.products');
Route::get('/pages/{slug}', [HomeController::class, 'page'])->name('page.show');

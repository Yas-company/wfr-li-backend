<?php

use App\Http\Controllers\api\v1\HomeController;
use Illuminate\Support\Facades\Route;

// Landing page route
Route::get('/', function () {
    return file_get_contents(public_path('landing/index.html'));
})->name('landing');

// Original routes moved to different paths
Route::get('/app', [HomeController::class, 'index'])->name('home');
Route::get('/category/{category}/products', [HomeController::class, 'categoryProducts'])->name('category.products');
Route::get('/pages/{slug}', [HomeController::class, 'page'])->name('page.show');

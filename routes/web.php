<?php

use App\Http\Controllers\api\v1\HomeController;
use App\Http\Controllers\api\v1\InterestController;
use Illuminate\Support\Facades\Route;

// Landing page route
Route::get('/', function () {
    $html = file_get_contents(public_path('landing/index.html'));
    // Replace the CSRF token placeholder with actual token
    $html = str_replace('{{ csrf_token() }}', csrf_token(), $html);
    return response($html)->header('Content-Type', 'text/html');
})->name('landing');

// Interest form submission route
Route::post('/api/interest', [InterestController::class, 'store'])->name('interest.store');

// Original routes moved to different paths
Route::get('/app', [HomeController::class, 'index'])->name('home');
Route::get('/category/{category}/products', [HomeController::class, 'categoryProducts'])->name('category.products');
Route::get('/pages/{slug}', [HomeController::class, 'page'])->name('page.show');

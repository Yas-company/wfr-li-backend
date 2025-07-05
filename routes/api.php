<?php

use App\Http\Controllers\api\v1\InterestController;
use Illuminate\Support\Facades\Route;

// Public routes (no CSRF protection needed)
Route::post('/interest', [InterestController::class, 'store'])->name('api.interest.store');

Route::prefix('v1')->group(function () {
    require base_path('routes/api/v1/auth.php');
    require base_path('routes/api/v1/setting.php');
    require base_path('routes/api/v1/lookup.php');
    require base_path('routes/api/v1/categories.php');
    require base_path('routes/api/v1/suppliers.php');
    require base_path('routes/api/v1/products.php');
    require base_path('routes/api/v1/favorites.php');
    require base_path('routes/api/v1/cart.php');
    require base_path('routes/api/v1/customer.php');
});

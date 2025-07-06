<?php

use Illuminate\Support\Facades\Route;



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
    require base_path('routes/api/v1/interest.php');
    require base_path('routes/api/v1/payment.php');
    require base_path('routes/api/v1/users.php');

});

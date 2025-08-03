<?php

use Illuminate\Support\Facades\Route;



Route::prefix('v1')->group(function () {
    require base_path('routes/api/v1/auth.php');
    require base_path('routes/api/v1/setting.php');
    require base_path('routes/api/v1/lookup.php');
    require base_path('routes/api/v1/categories.php');
    require base_path('routes/api/v1/suppliers.php');
    require base_path('routes/api/v1/products.php');

    require base_path('routes/api/v1/cart.php');
    require base_path('routes/api/v1/customer.php');
    require base_path('routes/api/v1/interest.php');
    require base_path('routes/api/v1/payment.php');
    require base_path('routes/api/v1/users.php');
    require base_path('routes/api/v1/orders.php');
    require base_path('routes/api/v1/ratings.php');
    require base_path('routes/api/v1/favorite.php');
    require base_path('routes/api/v1/metrics.php');
    require base_path('routes/api/v1/home.php');

    require base_path('routes/api/v1/organizations.php');
});

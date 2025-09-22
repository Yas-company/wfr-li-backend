<?php

use App\Http\Controllers\api\v1\PageController;
use Illuminate\Support\Facades\Route;

Route::prefix('pages')->group(function () {
    Route::get('/{page:slug}', [PageController::class, 'show'])->name('pages.show');
});


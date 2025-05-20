<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/sentiment-test', function () {
    return view('sentiment-test');
});

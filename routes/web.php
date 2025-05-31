<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatbotController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/sentiment-test', function () {
    return view('sentiment-test');
});

Route::get('/stock-history', [\App\Http\Controllers\StockHistoryController::class, 'show']);

Route::post('/chatbot/chat', [ChatbotController::class, 'chat']);

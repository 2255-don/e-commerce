<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/dashboard-test', function () {
    return view('dashboard');
});

Route::get('lang/{locale}', [App\Http\Controllers\LanguageController::class, 'swap']);

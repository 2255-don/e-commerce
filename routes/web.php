<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard')->middleware(['auth', 'verified']);

Route::get('lang/{locale}', [App\Http\Controllers\LanguageController::class, 'swap']);
Route::get('/profile', [App\Http\Controllers\UserController::class, 'show'])->name('profile.show')->middleware(['auth']);
Route::post('/profile/update', [App\Http\Controllers\UserController::class, 'update'])->name('profile.update')->middleware(['auth']);

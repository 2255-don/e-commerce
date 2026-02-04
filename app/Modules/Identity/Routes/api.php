<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Identity Module - API Routes
|--------------------------------------------------------------------------
|
| API routes for Identity domain
|
*/

Route::prefix('identity')->middleware('auth:sanctum')->group(function () {
    // API routes can be added here if needed
    // Example:
    // Route::get('/users', [\Modules\Identity\Controllers\Api\UserApiController::class, 'index']);
});

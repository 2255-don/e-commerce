<?php

use Illuminate\Support\Facades\Route;
use Modules\Identity\Controllers\Web\UserManagementController;
use Modules\Identity\Controllers\Web\RoleController;
use Modules\Identity\Controllers\Web\ProfilController;
use Modules\Identity\Controllers\Web\PermissionController;
use Modules\Identity\Controllers\Web\FeatureController;
use Modules\Identity\Controllers\Web\ModuleController;

/*
|--------------------------------------------------------------------------
| Identity Module - Web Routes
|--------------------------------------------------------------------------
|
| Routes for Identity domain (Users, Roles, Permissions, Modules, Features)
|
*/

Route::prefix('admin')->middleware(['auth', 'verified'])->group(function () {
    
    // User Management
    Route::get('/users', [UserManagementController::class, 'index'])->name('admin.users.index');
    Route::get('/users/{user}/roles', [UserManagementController::class, 'roles'])->name('admin.users.roles');
    Route::put('/users/{user}/roles', [UserManagementController::class, 'updateRoles'])->name('admin.users.roles.update');
    
    // Roles
    Route::resource('roles', RoleController::class)->names('admin.roles');
    Route::get('/roles/{role}/permissions', [RoleController::class, 'permissions'])->name('admin.roles.permissions');
    Route::put('/roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('admin.roles.permissions.update');
    
    // Profils
    Route::resource('profils', ProfilController::class)->names('admin.profils');
    
    // Permissions
    Route::resource('permissions', PermissionController::class)->names('admin.permissions');
    Route::post('/permissions/{permission}/features', [PermissionController::class, 'attachFeatures'])->name('admin.permissions.features.attach');
    Route::delete('/permissions/{permission}/features/{feature}', [PermissionController::class, 'detachFeature'])->name('admin.permissions.features.detach');
    
    // Features
    Route::resource('features', FeatureController::class)->names('admin.features');
    Route::post('/features/scan', [FeatureController::class, 'scanRoutes'])->name('admin.features.scan');
    
    // Modules
    Route::resource('modules', ModuleController::class)->names('admin.modules');
});

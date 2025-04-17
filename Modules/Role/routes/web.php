<?php

use Illuminate\Support\Facades\Auth; // Add Auth facade
use Illuminate\Support\Facades\Route;
// Keep the RoleController use statement for now, but use FQCN in routes
use Modules\Role\App\Http\Controllers\RoleController; 

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Add Authentication Routes provided by Laravel UI
// Make sure the controllers and views are correctly placed within this module
Auth::routes(['verify' => true]); // Enable email verification routes

// Group routes under 'admin' prefix and apply 'auth' middleware
Route::group(['middleware' => ['auth'], 'prefix' => 'admin'], function () {
    // Apply specific role/permission middleware to the routes
    // Use Fully Qualified Class Name (FQCN) for clarity
    Route::resource('role', \Modules\Role\App\Http\Controllers\RoleController::class)
        ->names('admin.role') // Use a distinct name prefix like 'admin.role'
        ->middleware('permission:view role|create role|update role|delete role');

    // Specific delete route
    Route::get('role/{roleId}/delete', [\Modules\Role\App\Http\Controllers\RoleController::class, 'destroy'])
        ->name('admin.role.delete') // Add a name
        ->middleware('permission:delete role');

    // Routes for adding/giving permissions to roles
    Route::get('role/{roleId}/give-permissions', [\Modules\Role\App\Http\Controllers\RoleController::class, 'addPermissionToRole'])
        ->name('admin.role.addPermissions') // Add a name
        ->middleware('permission:update role'); // Assuming 'update role' covers managing permissions

    Route::put('role/{roleId}/give-permissions', [\Modules\Role\App\Http\Controllers\RoleController::class, 'givePermissionToRole'])
        ->name('admin.role.givePermissions') // Add a name
        ->middleware('permission:update role'); // Assuming 'update role' covers managing permissions
});

// Note: The user management routes (index, create, edit, delete) from the main web.php
// are already pointing to the Role module's UserController.
// We might need to adjust prefixes or middleware later if needed.

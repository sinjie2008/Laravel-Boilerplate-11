<?php

use Illuminate\Support\Facades\Route;
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

// Group routes under 'admin' prefix and apply 'auth' middleware
Route::group(['middleware' => ['auth'], 'prefix' => 'admin'], function () {
    // Apply specific role/permission middleware to the routes
    Route::resource('role', RoleController::class)
        ->names('admin.role') // Use a distinct name prefix like 'admin.role'
        ->middleware('permission:view role|create role|update role|delete role');

    // Specific delete route (using GET for simplicity, though DELETE via form is better practice)
    // Note: The index view was updated to use a form with DELETE method, which is preferred.
    // This GET route might be redundant if the form method is used consistently.
    // Keeping it for now to match the original structure, but consider removing if not needed.
    Route::get('role/{roleId}/delete', [RoleController::class, 'destroy'])
        ->name('admin.role.delete') // Add a name
        ->middleware('permission:delete role');

    // Routes for adding/giving permissions to roles
    Route::get('role/{roleId}/give-permissions', [RoleController::class, 'addPermissionToRole'])
        ->name('admin.role.addPermissions') // Add a name
        ->middleware('permission:update role'); // Assuming 'update role' covers managing permissions

    Route::put('role/{roleId}/give-permissions', [RoleController::class, 'givePermissionToRole'])
        ->name('admin.role.givePermissions') // Add a name
        ->middleware('permission:update role'); // Assuming 'update role' covers managing permissions
});

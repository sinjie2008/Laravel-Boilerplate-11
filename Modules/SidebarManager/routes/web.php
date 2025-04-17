<?php

use Illuminate\Support\Facades\Route;
use Modules\SidebarManager\App\Http\Controllers\SidebarManagerController;
use Modules\SidebarManager\App\Http\Controllers\SidebarItemController;

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

// Route::group([], function () {
//     Route::resource('sidebarmanager', SidebarManagerController::class)->names('sidebarmanager');
// });

// Add CRUD routes for Sidebar Items, protected by auth middleware and prefixed
Route::middleware(['web', 'auth']) // Ensure web middleware and authentication
    ->prefix('admin/sidebar') // Prefix for admin sidebar management routes
    ->name('admin.sidebar.') // Route name prefix
    ->group(function () {
        Route::resource('items', SidebarItemController::class)
            ->except(['show']); // Exclude 'show' route if not needed

        // Route for updating the order
        Route::post('items/update-order', [SidebarItemController::class, 'updateOrder'])->name('items.updateOrder');
    });

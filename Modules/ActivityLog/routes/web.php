<?php

use Illuminate\Support\Facades\Route;
use Modules\ActivityLog\App\Http\Controllers\ActivityLogController;

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

// Apply auth, verified, and permission middleware to the route group
Route::middleware(['auth', 'verified', 'permission:view activity logs'])->group(function () {
    // Define only the index route for activity logs
    Route::get('admin/activitylog', [ActivityLogController::class, 'index'])->name('activitylog.index'); // Changed path to admin/activitylog
});

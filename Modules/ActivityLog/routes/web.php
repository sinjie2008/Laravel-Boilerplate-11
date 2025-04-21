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

Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('activity-log', [ActivityLogController::class, 'index'])->name('activitylog.index');
    // We only need the index view for now
    // Route::resource('activitylog', ActivityLogController::class)->names('activitylog');
});

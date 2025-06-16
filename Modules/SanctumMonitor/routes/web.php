<?php

use Illuminate\Support\Facades\Route;
use Modules\SanctumMonitor\App\Http\Controllers\SanctumMonitorController;

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

Route::prefix('admin/sanctum-manager')
    ->middleware(['web', 'auth', 'role:admin|super-admin'])
    ->name('admin.sanctummonitor.')
    ->group(function () {
        Route::get('/', [SanctumMonitorController::class, 'index'])->name('dashboard');
        Route::get('/tokens', [SanctumMonitorController::class, 'tokens'])->name('tokens');
        Route::get('/tokens/data', [SanctumMonitorController::class, 'tokensData'])->name('tokens.data');
        Route::delete('/tokens/{tokenId}', [SanctumMonitorController::class, 'revoke'])->name('tokens.revoke');
        Route::get('/activity', [SanctumMonitorController::class, 'activity'])->name('activity');
        Route::get('/activity/data', [SanctumMonitorController::class, 'activityData'])->name('activity.data');
        Route::get('/stats', [SanctumMonitorController::class, 'stats'])->name('stats');
        Route::get('/logs', [SanctumMonitorController::class, 'logs'])->name('logs');
        Route::get('/logs/data', [SanctumMonitorController::class, 'logsData'])->name('logs.data');
        Route::match(['get', 'post'], '/settings', [SanctumMonitorController::class, 'settings'])->name('settings');
    });

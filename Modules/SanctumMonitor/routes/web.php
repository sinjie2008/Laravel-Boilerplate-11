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
    ->middleware(['web', 'auth', 'role:admin'])
    ->name('admin.sanctummonitor.')
    ->group(function () {
        Route::get('/', [SanctumMonitorController::class, 'index'])->name('dashboard');
        Route::get('/tokens', [SanctumMonitorController::class, 'tokens'])->name('tokens');
        Route::delete('/tokens/{tokenId}', [SanctumMonitorController::class, 'revoke'])->name('tokens.revoke');
        Route::get('/activity', [SanctumMonitorController::class, 'activity'])->name('activity');
        Route::get('/stats', [SanctumMonitorController::class, 'stats'])->name('stats');
        Route::get('/logs', [SanctumMonitorController::class, 'logs'])->name('logs');
        Route::match(['get', 'post'], '/settings', [SanctumMonitorController::class, 'settings'])->name('settings');
    });

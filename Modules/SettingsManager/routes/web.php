<?php

use Illuminate\Support\Facades\Route;
use Modules\SettingsManager\App\Http\Controllers\SettingsController;

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

Route::prefix('admin')->middleware(['auth', 'web'])->group(function () {
    Route::get('settings-manager', [SettingsController::class, 'index'])->name('admin.settings-manager.index');
    Route::post('settings-manager', [SettingsController::class, 'update'])->name('admin.settings-manager.update');
});

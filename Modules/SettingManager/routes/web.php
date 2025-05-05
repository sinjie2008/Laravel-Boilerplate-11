<?php

use Illuminate\Support\Facades\Route;
use Modules\SettingManager\App\Http\Controllers\SettingManagerController;

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

Route::middleware(['auth'])->prefix('admin/settings')->name('settings.')->group(function () {
    Route::get('/', [SettingManagerController::class, 'index'])->name('index');
    Route::put('/', [SettingManagerController::class, 'update'])->name('update'); // Using PUT for update
});

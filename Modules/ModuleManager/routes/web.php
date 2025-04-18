<?php

use Illuminate\Support\Facades\Route;
use Modules\ModuleManager\App\Http\Controllers\ModuleManagerController;

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

Route::group(['middleware' => ['web', 'auth']], function () { // Added auth middleware
    Route::prefix('admin/module-manager')->name('module-manager.')->group(function() { // Changed prefix to admin/module-manager
        Route::get('/', [ModuleManagerController::class, 'index'])->name('index');
        Route::post('/upload', [ModuleManagerController::class, 'upload'])->name('upload'); // Add upload route
        Route::post('/activate/{module}', [ModuleManagerController::class, 'activate'])->name('activate');
        Route::post('/deactivate/{module}', [ModuleManagerController::class, 'deactivate'])->name('deactivate');
        Route::post('/uninstall/{module}', [ModuleManagerController::class, 'uninstall'])->name('uninstall'); // Consider security implications
    });
});

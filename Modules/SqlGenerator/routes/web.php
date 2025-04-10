<?php

use Illuminate\Support\Facades\Route;
use Modules\SqlGenerator\App\Http\Controllers\SqlGeneratorController;

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

// Prefix with 'admin' and apply 'auth' middleware
Route::group(['middleware' => ['auth', 'permission:view sqlgenerator'], 'prefix' => 'admin'], function () {
    // Define routes for the SQL Generator with hyphen
    Route::get('sql-generator', [SqlGeneratorController::class, 'index'])->name('admin.sql-generator.index'); // Updated path and name
    Route::post('sql-generator', [SqlGeneratorController::class, 'generate'])->name('admin.sql-generator.generate'); // Updated path and name
});

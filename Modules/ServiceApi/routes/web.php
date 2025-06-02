<?php

use Illuminate\Support\Facades\Route;
use Modules\ServiceApi\Http\Controllers\ServiceWebController;

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

Route::prefix('admin')->group(function () {
    Route::resource('serviceapi-manager', ServiceWebController::class)->names('serviceapi.services');
});

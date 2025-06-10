<?php

use Illuminate\Support\Facades\Route;
use Modules\EmailManager\Http\Controllers\EmailManagerController;

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
    Route::resource('email-manager', EmailManagerController::class)->names('admin.email-manager');
    Route::post('email-manager/test-send', [EmailManagerController::class, 'testSend'])->name('admin.email-manager.test-send');
});

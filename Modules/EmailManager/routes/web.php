<?php

use Illuminate\Support\Facades\Route;
use Modules\EmailManager\App\Http\Controllers\EmailManagerController;

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

Route::middleware(['auth'])->prefix('admin/email-manager')->name('admin.email-manager.')->group(function () {
    Route::get('/', [EmailManagerController::class, 'index'])->name('index');
    Route::post('/update', [EmailManagerController::class, 'update'])->name('update');
    Route::post('/send-test-email', [EmailManagerController::class, 'sendTestEmail'])->name('send-test-email');
});

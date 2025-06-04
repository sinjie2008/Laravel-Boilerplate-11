<?php

use Illuminate\Support\Facades\Route;
use Modules\FundRequest\Http\Controllers\FundRequestController;

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

Route::middleware(['auth'])->prefix('admin/fund-requests')->name('admin.fundrequests.')->group(function () {
    Route::get('/', [FundRequestController::class, 'index'])->name('index');
    Route::get('/create', [FundRequestController::class, 'create'])->name('create');
    Route::post('/', [FundRequestController::class, 'store'])->name('store');
    Route::get('/{fundRequest}', [FundRequestController::class, 'show'])->name('show');
    Route::get('/{fundRequest}/edit', [FundRequestController::class, 'edit'])->name('edit');
    Route::put('/{fundRequest}', [FundRequestController::class, 'update'])->name('update');
    Route::delete('/{fundRequest}', [FundRequestController::class, 'destroy'])->name('destroy');

    // Approval routes
    Route::post('/{fundRequest}/approve', [FundRequestController::class, 'approve'])->name('approve');
    Route::post('/{fundRequest}/reject', [FundRequestController::class, 'reject'])->name('reject');
});

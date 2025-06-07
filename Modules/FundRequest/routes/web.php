<?php

use Illuminate\Support\Facades\Route;
use Modules\FundRequest\App\Http\Controllers\FundRequestController;

Route::group(['prefix' => 'admin'], function () {
    Route::resource('fund-request', FundRequestController::class)->names('admin.fund-request');
    Route::post('fund-request/{id}/approve', [FundRequestController::class, 'approve'])->name('admin.fund-request.approve');
    Route::post('fund-request/{id}/reject', [FundRequestController::class, 'reject'])->name('admin.fund-request.reject');
    Route::post('fund-request/{id}/final-approve', [FundRequestController::class, 'finalApprove'])->name('admin.fund-request.final-approve');
    Route::post('fund-request/{id}/final-reject', [FundRequestController::class, 'finalReject'])->name('admin.fund-request.final-reject');
});

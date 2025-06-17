<?php

use Illuminate\Support\Facades\Route;
use Modules\BillingManager\App\Http\Controllers\Admin;

Route::middleware(['auth', 'can:manage-billing'])
    ->prefix('admin/billing')
    ->name('admin.billing.')
    ->group(function () {
        Route::get('/', Admin\DashboardController::class)->name('index');
        Route::resource('plans', Admin\PlanController::class)->except('show');
        Route::post('/refund/{invoice}', [Admin\RefundController::class, 'store'])->name('refund');
    });

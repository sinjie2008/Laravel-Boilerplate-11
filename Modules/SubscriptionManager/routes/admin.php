<?php

use Illuminate\Support\Facades\Route;
use Modules\SubscriptionManager\Http\Controllers\Admin\PlanController;
use Modules\SubscriptionManager\Http\Controllers\Admin\UserSubscriptionController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" and "auth" middleware group. Now create something great!
|
*/

Route::middleware(['web', 'auth', 'verified']) // Assuming admin routes require auth
    ->prefix('admin/subscription-manager')
    ->as('admin.')
    ->group(function () {
        Route::resource('plans', PlanController::class);
        Route::resource('subscriptions', UserSubscriptionController::class);
        Route::post('subscriptions/{subscription}/regenerate-token', [UserSubscriptionController::class, 'regenerateToken'])
            ->name('subscriptions.regenerateToken');
    });
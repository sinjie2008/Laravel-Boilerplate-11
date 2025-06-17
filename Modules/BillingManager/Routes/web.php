<?php

use Illuminate\Support\Facades\Route;
use Modules\BillingManager\App\Http\Controllers\Front;

Route::middleware('auth')
    ->prefix('billing')
    ->name('billing.')
    ->group(function () {
        Route::get('/', Front\DashboardController::class)->name('index');
        Route::post('/subscribe', [Front\SubscriptionController::class, 'store'])->name('subscribe');
        Route::post('/cancel', [Front\SubscriptionController::class, 'cancel'])->name('cancel');
        Route::post('/resume', [Front\SubscriptionController::class, 'resume'])->name('resume');
        Route::get('/invoices/{invoice}', Front\InvoiceController::class)->name('invoice');
    });

Route::post('/cashier/webhook', function () {
    dispatch(new Modules\BillingManager\App\Jobs\HandleStripeWebhookJob(request()->all()));
    return response()->json(['status' => 'ok']);
});

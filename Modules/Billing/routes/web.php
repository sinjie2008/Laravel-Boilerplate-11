<?php

use Illuminate\Support\Facades\Route;
use Modules\Billing\App\Http\Controllers\AuditLogController;
use Modules\Billing\App\Http\Controllers\BillingController;
use Modules\Billing\App\Http\Controllers\CouponController;
use Modules\Billing\App\Http\Controllers\ImpersonationController;
use Modules\Billing\App\Http\Controllers\MetricsController;
use Modules\Billing\App\Http\Controllers\PastDueController;
use Modules\Billing\App\Http\Controllers\PlanController;
use Modules\Billing\App\Http\Controllers\SettingsController;
use Modules\Billing\App\Http\Controllers\SubscriptionController;
use Modules\Billing\App\Http\Controllers\WebhookController;
use Modules\Billing\App\Jobs\HandleStripeWebhook;
use Modules\Billing\App\Models\WebhookLog;

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

Route::middleware(['web', 'auth', 'can:view-billing'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('billing', [BillingController::class, 'index'])->name('billing.index');
        Route::get('billing/{invoice}', [BillingController::class, 'show'])->name('billing.show');
        Route::get('billing/webhooks', [WebhookController::class, 'index'])->name('webhooks.index');
        Route::get('billing/past-due', [PastDueController::class, 'index'])->name('past-due.index');
        Route::get('billing/past-due/{subscription}', [PastDueController::class, 'show'])->name('past-due.show');
        Route::get('billing/metrics', [MetricsController::class, 'index'])->name('metrics.index');
        Route::get('billing/metrics/export/{metric}', [MetricsController::class, 'export'])->name('metrics.export');
        Route::resource('billing/subscriptions', SubscriptionController::class)->only(['index', 'show'])->names('subscriptions');
        Route::get('billing/audit-log', [AuditLogController::class, 'index'])->name('audit.index');
    });

Route::middleware(['web', 'auth', 'can:manage-billing'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::post('billing/webhook', function () {
            $payload = request()->all();
            WebhookLog::create([
                'event_type' => $payload['type'] ?? 'unknown',
                'payload' => $payload,
            ]);

            HandleStripeWebhook::dispatch($payload);

            return response()->json(['status' => 'queued']);
        })->name('billing.webhook');

        Route::post('billing/webhooks/{webhook}/replay', [WebhookController::class, 'replay'])->name('webhooks.replay');

        Route::resource('billing', BillingController::class)->except(['index', 'show'])->names('billing');
        Route::resource('billing/plans', PlanController::class)->names('plans');
        Route::resource('billing/coupons', CouponController::class)->names('coupons');
        Route::post('billing/refund/{invoice}', [BillingController::class, 'refund'])->name('billing.refund');
        Route::post('billing/past-due/{subscription}/retry', [PastDueController::class, 'retry'])->name('past-due.retry');
        Route::get('billing/settings', [SettingsController::class, 'index'])->name('billing.settings.index');
        Route::post('billing/settings', [SettingsController::class, 'update'])->name('billing.settings.update');
        Route::post('billing/impersonate/{user}', [ImpersonationController::class, 'start'])->name('impersonate.start');
        Route::post('billing/impersonate/stop', [ImpersonationController::class, 'stop'])->name('impersonate.stop');
    });

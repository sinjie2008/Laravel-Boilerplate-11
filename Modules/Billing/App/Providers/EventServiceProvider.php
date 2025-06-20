<?php

namespace Modules\Billing\App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    public function boot(): void
    {
        parent::boot();

        \Modules\Billing\App\Models\Invoice::observe(\Modules\Billing\App\Observers\InvoiceObserver::class);
        \Modules\Billing\App\Models\Plan::observe(\Modules\Billing\App\Observers\PlanObserver::class);
        \Modules\Billing\App\Models\Coupon::observe(\Modules\Billing\App\Observers\CouponObserver::class);
        \Modules\Billing\App\Models\Subscription::observe(\Modules\Billing\App\Observers\SubscriptionObserver::class);
    }

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void
    {
        //
    }
}

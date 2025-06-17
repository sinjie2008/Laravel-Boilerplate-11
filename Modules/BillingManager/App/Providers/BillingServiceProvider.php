<?php

declare(strict_types=1);

namespace Modules\BillingManager\App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\CashierServiceProvider;
use Modules\BillingManager\App\Jobs\HandleStripeWebhookJob;

class BillingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->register(CashierServiceProvider::class);
        $this->mergeConfigFrom(__DIR__.'/../../Config/cashier.php', 'cashier');
        $this->mergeConfigFrom(__DIR__.'/../../Config/services.php', 'services');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(module_path('BillingManager', 'Database/Migrations'));
        $this->loadRoutesFrom(__DIR__.'/../../Routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../../Routes/admin.php');
        $this->loadRoutesFrom(__DIR__.'/../../Routes/api.php');
        $this->loadViewsFrom(module_path('BillingManager', 'Resources/views'), 'billing');

        Gate::define('manage-billing', fn (\Modules\Role\App\Models\User $u) => $u->hasAnyRole(['admin', 'super-admin']));
    }
}

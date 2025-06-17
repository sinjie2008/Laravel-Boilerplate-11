<?php

declare(strict_types=1);

namespace Modules\BillingManager\App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        parent::boot();
    }

    public function map(): void
    {
        $this->mapWebRoutes();
        $this->mapAdminRoutes();
        $this->mapApiRoutes();
    }

    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->namespace('Modules\\BillingManager\\App\\Http\\Controllers')
            ->group(module_path('BillingManager', 'Routes/web.php'));
    }

    protected function mapAdminRoutes(): void
    {
        Route::middleware('web')
            ->namespace('Modules\\BillingManager\\App\\Http\\Controllers')
            ->group(module_path('BillingManager', 'Routes/admin.php'));
    }

    protected function mapApiRoutes(): void
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace('Modules\\BillingManager\\App\\Http\\Controllers')
            ->group(module_path('BillingManager', 'Routes/api.php'));
    }
}

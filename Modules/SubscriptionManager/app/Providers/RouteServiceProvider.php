<?php

namespace Modules\SubscriptionManager\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'SubscriptionManager';

    /**
     * The module namespace to assume when generating URLs to actions.
     * @var string
     */
    protected string $moduleNamespace = 'Modules\SubscriptionManager\Http\Controllers';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
        $this->mapAdminRoutes(); // Added this line
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->namespace($this->moduleNamespace) // Ensure controller namespace is correct
            ->group(module_path($this->name, '/routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapApiRoutes(): void
    {
        Route::middleware('api')
            ->prefix('api')
            ->name('api.')
            ->namespace($this->moduleNamespace) // Ensure controller namespace is correct
            ->group(module_path($this->name, '/routes/api.php'));
    }

    /**
     * Define the "admin" routes for the application.
     *
     * These routes are typically for the admin panel.
     */
    protected function mapAdminRoutes(): void // Added this method
    {
        Route::middleware('web') // Base middleware, specific auth is in admin.php
             ->namespace($this->moduleNamespace . '\Admin') // Namespace for Admin controllers
             ->group(module_path($this->name, '/routes/admin.php'));
    }
}

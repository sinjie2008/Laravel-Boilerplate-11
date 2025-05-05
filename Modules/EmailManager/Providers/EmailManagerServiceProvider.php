<?php

namespace Modules\EmailManager\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class EmailManagerServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path('EmailManager', 'Database/Migrations'));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path('EmailManager', 'config/config.php') => config_path('emailmanager.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('EmailManager', 'config/config.php'), 'emailmanager'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/emailmanager');

        $sourcePath = module_path('EmailManager', 'resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', 'emailmanager-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), 'emailmanager');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/emailmanager');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'emailmanager');
            $this->loadJsonTranslationsFrom($langPath, 'emailmanager');
        } else {
            $this->loadTranslationsFrom(module_path('EmailManager', 'resources/lang'), 'emailmanager');
            $this->loadJsonTranslationsFrom(module_path('EmailManager', 'resources/lang'), 'emailmanager');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/emailmanager')) {
                $paths[] = $path . '/modules/emailmanager';
            }
        }
        return $paths;
    }
}

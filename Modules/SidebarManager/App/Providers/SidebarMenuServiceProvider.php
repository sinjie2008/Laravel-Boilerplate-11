<?php

namespace Modules\SidebarManager\App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Modules\SidebarManager\App\Services\SidebarService;
use Illuminate\Support\Facades\Log;

class SidebarMenuServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     */
    public function boot(Dispatcher $events, SidebarService $sidebarService): void
    {
        $this->registerConfig();
        $this->registerViews();
        $this->registerTranslations();
        $this->loadMigrationsFrom(module_path('SidebarManager', 'Database/migrations'));

        // Listen for the AdminLTE menu building event
        $events->listen(BuildingMenu::class, function (BuildingMenu $event) use ($sidebarService) {
            Log::info('[SidebarManager] BuildingMenu event listener triggered.');
            try {
                // Get menu items from the database via SidebarService
                $menuItems = $sidebarService->getMenuItems();
                Log::info('[SidebarManager] Fetched menu items from Service:', $menuItems);

                // Add items to the sidebar menu
                if (!empty($menuItems)) {
                    $event->menu->add(...$menuItems);
                    Log::info('[SidebarManager] Added ' . count($menuItems) . ' items to menu.');
                } else {
                    Log::warning('[SidebarManager] No menu items returned from SidebarService to add.');
                }
            } catch (\Exception $e) {
                Log::error('[SidebarManager] Error in BuildingMenu listener: ' . $e->getMessage());
            }
        });
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);

        // Register the SidebarService as a singleton
        $this->app->singleton(SidebarService::class, function ($app) {
            return new SidebarService();
        });
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $this->publishes([
            module_path('SidebarManager', 'config/config.php') => config_path('sidebarmanager.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('SidebarManager', 'config/config.php'), 'sidebarmanager'
        );
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/sidebarmanager');

        $sourcePath = module_path('SidebarManager', 'resources/views');

        $this->publishes([
            $sourcePath => $viewPath,
        ], ['views', 'sidebarmanager-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), 'sidebarmanager');
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/sidebarmanager');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'sidebarmanager');
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path('SidebarManager', 'lang'), 'sidebarmanager');
            $this->loadJsonTranslationsFrom(module_path('SidebarManager', 'lang'));
        }
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [SidebarService::class];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path.'/modules/sidebarmanager')) {
                $paths[] = $path.'/modules/sidebarmanager';
            }
        }

        return $paths;
    }
}

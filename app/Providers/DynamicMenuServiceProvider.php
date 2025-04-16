<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher; // Correct Dispatcher import
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Nwidart\Modules\Facades\Module; // Import Module facade

class DynamicMenuServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(Dispatcher $events): void // Inject Dispatcher
    {
        $events->listen(BuildingMenu::class, function (BuildingMenu $event) {
            // Define menu items for manageable modules found in the Modules directory
            // Key should be the exact module name (case-sensitive)
            $moduleMenuItems = [
                'TodoList' => [
                    // 'header' => 'User Management', // Headers are added once
                    'text' => 'TodoList',
                    'url'  => 'admin/todolist', // Confirm this URL/route is correct
                    'icon' => 'fas fa-fw fa-list-alt',
                    'order' => 15 // Place after 'Users' (assuming Users is ~10)
                ],
                'ActivityLog' => [
                    // 'header' => 'User Management',
                    'text' => 'Activity Logs',
                    'route'  => 'activitylog.index', // Use the named route from the module
                    'icon' => 'fas fa-history',
                    'can'  => 'view activity logs', // Keep permission check
                    'order' => 45 // Place after 'Permissions' (assuming Permissions is ~40)
                ],
                 'SqlGenerator' => [
                    // 'header' => 'User Management',
                    'text' => 'SQL Generator',
                    'url'  => 'admin/sql-generator', // Confirm this URL/route is correct
                    'icon' => 'fas fa-database',
                    'order' => 55 // Place after 'Activity Logs'
                ],
                'Document' => [ // Add entry for the Document module
                    'text' => 'Documents',
                    'route'  => 'document.documents.index', // Use the named route from the module
                    'icon' => 'fas fa-fw fa-file-alt', // Document icon
                    'can'  => 'view documents', // Add permission check
                    'order' => 50 // Place it after Activity Logs, before SQL Generator
                ],
                'BackupManager' => [ // Add entry for the BackupManager module
                    'text' => 'Backup Manager',
                    'route'  => 'backup-manager.index', // Use the named route from the module
                    'icon' => 'fas fa-hdd', // Hard drive icon for backups
                    // 'can'  => 'manage backups', // Optional permission check
                    'order' => 60 // Place it after SQL Generator
                ],
                // Note: 'Role' seems to be core or handled differently, not included here.
                // 'ModuleManager' is handled statically in config/adminlte.php
            ];

            // Get all enabled modules
            $enabledModules = Module::allEnabled();

            $dynamicItems = [];
            foreach ($enabledModules as $module) {
                $moduleName = $module->getName();
                // Exclude ModuleManager itself from dynamic adding
                if (strtolower($moduleName) !== 'modulemanager' && isset($moduleMenuItems[$moduleName])) {
                    $dynamicItems[] = $moduleMenuItems[$moduleName];
                }
            }

            // Add the dynamic items to the menu
            // The 'order' property should place them correctly relative to static items
            foreach ($dynamicItems as $item) {
                 $event->menu->add($item);
            }

            // Note: If headers are needed specifically for dynamic items,
            // you might need more complex logic to find insertion points.
            // Using 'order' is generally sufficient if static items also have orders.
        });
    }
}

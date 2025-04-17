<?php

namespace Modules\SidebarManager\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\SidebarManager\App\Models\SidebarItem;

class SidebarItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure table is empty before seeding
        SidebarItem::query()->delete();

        // Level 1 Items
        $dashboard = SidebarItem::create([
            'name' => 'Dashboard',
            'icon' => 'fas fa-fw fa-tachometer-alt',
            'route' => 'home', // Assuming 'home' is your dashboard route
            'order' => 1,
            'enabled' => true,
        ]);

        // --- Engineering Management Section ---
        $engineeringManagement = SidebarItem::create([
            'name' => 'Engineering Management',
            'icon' => 'fas fa-fw fa-cogs', // Choose an appropriate icon
            'route' => null,
            'order' => 10, // << New section order
            'enabled' => true,
        ]);

        SidebarItem::create([
            'parent_id' => $engineeringManagement->id,
            'name' => 'TodoList',
            'icon' => 'fas fa-fw fa-tasks', // Choose an appropriate icon
            'route' => 'admin/todolist', // Adjust route name if needed
            'order' => 11, // << New item order
            // 'permission_required' => 'view todolist', // Example permission
            'enabled' => true,
        ]);

        SidebarItem::create([
            'parent_id' => $engineeringManagement->id,
            'name' => 'Documents',
            'icon' => 'fas fa-fw fa-file-alt', // Choose an appropriate icon
            'route' => 'admin/documents', // Adjust route name if needed
            'order' => 12, // << New item order
            // 'permission_required' => 'view documents', // Example permission
            'enabled' => true,
        ]);


        // --- User Management Section ---
        $userManagement = SidebarItem::create([
            'name' => 'User Management',
            'icon' => 'fas fa-fw fa-users-cog', // Icon for the main section
            'route' => null, // No direct route, it's a parent container
            'order' => 20, // << Updated order
            'enabled' => true,
        ]);

        // Level 2 Items under User Management
        SidebarItem::create([
            'parent_id' => $userManagement->id,
            'name' => 'Users',
            'icon' => 'fas fa-fw fa-users',
            'route' => 'admin/users', // Adjust route name if needed
            'order' => 21, // << Updated order
            // 'permission_required' => 'view users', // Example permission
            'enabled' => true,
        ]);

        SidebarItem::create([
            'parent_id' => $userManagement->id,
            'name' => 'Role', // << Corrected name from Roles
            'icon' => 'fas fa-fw fa-user-shield',
            'route' => 'admin/role', // Adjust route name if needed
            'order' => 22, // << Updated order
            // 'permission_required' => 'view role', // Example permission
            'enabled' => true,
        ]);

        SidebarItem::create([
            'parent_id' => $userManagement->id,
            'name' => 'Permissions',
            'icon' => 'fas fa-fw fa-key',
            'route' => 'admin/permissions', // Adjust route name if needed
            'order' => 23, // << Updated order
            // 'permission_required' => 'view permissions', // Example permission
            'enabled' => true,
        ]);

        // --- System Section ---
        $system = SidebarItem::create([
            'name' => 'System',
            'icon' => 'fas fa-cogs',
            'route' => null,
            'order' => 30, // << Updated order
            'enabled' => true,
        ]);

        SidebarItem::create([
            'parent_id' => $system->id,
            'name' => 'SidebarManager', // << Added item
            'icon' => 'fas fa-fw fa-list-alt', // Choose an appropriate icon
            'route' => 'admin/sidebar/items', // Adjust route name if needed (based on routes/web.php)
            'order' => 31, // << New item order
            // 'permission_required' => 'manage sidebar', // Example permission
            'enabled' => true,
        ]);

        SidebarItem::create([
            'parent_id' => $system->id,
            'name' => 'MediaManager', // << Added item
            'icon' => 'fas fa-fw fa-photo-video', // Choose an appropriate icon
            'route' => 'admin/media-manager', // Adjust route name if needed
            'order' => 32, // << New item order
            // 'permission_required' => 'manage media', // Example permission
            'enabled' => true,
        ]);

        SidebarItem::create([
            'parent_id' => $system->id,
            'name' => 'ModuleManager', // << Corrected Name
            'icon' => 'fas fa-puzzle-piece',
            'route' => 'admin/module-manager', // Adjust route name if needed
            'order' => 33, // << Updated order
             // 'permission_required' => 'manage modules', // Example permission
            'enabled' => true,
        ]);

        SidebarItem::create([
            'parent_id' => $system->id,
            'name' => 'BackupManager', // << Added item
            'icon' => 'fas fa-fw fa-hdd', // Choose an appropriate icon
            'route' => 'admin/backup-manager', // Adjust route name if needed
            'order' => 34, // << New item order
            // 'permission_required' => 'manage backups', // Example permission
            'enabled' => true,
        ]);

        SidebarItem::create([
            'parent_id' => $system->id,
            'name' => 'SqlGenerator', // << Added item
            'icon' => 'fas fa-fw fa-database', // Choose an appropriate icon
            'route' => 'admin/sql-generator', // Adjust route name if needed
            'order' => 35, // << New item order
            // 'permission_required' => 'use sqlgenerator', // Example permission
            'enabled' => true,
        ]);

        // Add more items as needed...
    }
}

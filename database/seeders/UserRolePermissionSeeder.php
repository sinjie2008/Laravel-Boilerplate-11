<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class UserRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Permissions
        $permissions = [
            'view role', 'create role', 'update role', 'delete role',
            'view permission', 'create permission', 'update permission', 'delete permission',
            'view user', 'create user', 'update user', 'delete user',
            'view product', 'create product', 'update product', 'delete product',
            'view activity logs', 'view sqlgenerator',
            'view documents', 'create documents', 'update documents', 'delete documents',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $staffRole = Role::firstOrCreate(['name' => 'staff']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Assign Permissions to Roles
        $allPermissionNames = Permission::pluck('name')->toArray();
        $superAdminRole->syncPermissions($allPermissionNames);

        $adminRole->syncPermissions([
            'create role', 'view role', 'update role',
            'create permission', 'view permission',
            'create user', 'view user', 'update user',
            'create product', 'view product', 'update product',
            'create documents', 'view documents', 'update documents',
            'view activity logs', 'view sqlgenerator',
        ]);

        // Create Users and Assign Roles
        $superAdminUser = User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            ['name' => 'Super Admin', 'password' => Hash::make('12345678')]
        );
        $superAdminUser->assignRole($superAdminRole);

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            ['name' => 'Admin', 'password' => Hash::make('12345678')]
        );
        $adminUser->assignRole($adminRole);

        $staffUser = User::firstOrCreate(
            ['email' => 'staff@gmail.com'],
            ['name' => 'Staff', 'password' => Hash::make('12345678')]
        );
        $staffUser->assignRole($staffRole);
    }
}

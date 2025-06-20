<?php

namespace Modules\Billing\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class BillingDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $view = Permission::firstOrCreate(['name' => 'view-billing']);
        $manage = Permission::firstOrCreate(['name' => 'manage-billing']);

        Role::findOrCreate('billing-manager')->syncPermissions([$view, $manage]);
        Role::findOrCreate('admin')->syncPermissions([$view, $manage]);
        Role::findOrCreate('super-admin')->syncPermissions([$view, $manage]);
    }
}

<?php

namespace Modules\SidebarManager\Database\Seeders;

use Illuminate\Database\Seeder;

class SidebarManagerDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            SidebarItemsSeeder::class,
        ]);

        // $this->call("OthersTableSeeder");
    }
}

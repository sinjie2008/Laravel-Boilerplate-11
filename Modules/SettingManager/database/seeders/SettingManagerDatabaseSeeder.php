<?php

namespace Modules\SettingManager\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingManagerDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'site_name', 'value' => 'My Laravel App'],
            ['key' => 'site_description', 'value' => 'Simple Laravel application'],
            ['key' => 'site_logo', 'value' => '/storage/logo.png'],
            ['key' => 'timezone', 'value' => 'Asia/Singapore'],
            ['key' => 'locale', 'value' => 'en'],
            ['key' => 'maintenance_mode', 'value' => 'false'],
            ['key' => 'company_email', 'value' => 'info@example.com'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}

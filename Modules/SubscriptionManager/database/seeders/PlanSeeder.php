<?php

namespace Modules\SubscriptionManager\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\SubscriptionManager\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plan::firstOrCreate(
            ['name' => 'Free'],
            ['price' => 0.00, 'api_call_limit_per_day' => 100]
        );

        Plan::firstOrCreate(
            ['name' => 'Basic'],
            ['price' => 9.99, 'api_call_limit_per_day' => 1000]
        );

        Plan::firstOrCreate(
            ['name' => 'Pro'],
            ['price' => 29.99, 'api_call_limit_per_day' => 10000]
        );
    }
}

<?php

namespace Modules\FundRequest\Database\Seeders;

use Illuminate\Database\Seeder;

class FundRequestDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(FundRequestApprovalSeeder::class);
    }
}

<?php

namespace Modules\Approval\Database\Seeders;

use Illuminate\Database\Seeder;
use RingleSoft\LaravelProcessApproval\ProcessApproval;
use Spatie\Permission\Models\Role;
use Modules\Approval\App\Models\ApprovalItem;

class ApprovalDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $flow = ProcessApproval::createFlow('Default ApprovalItem Flow', ApprovalItem::class);
        } catch (\Throwable $e) {
            $flow = ProcessApproval::flowsWithSteps()->firstWhere('approvable_type', ApprovalItem::class);
        }

        $staffRole = Role::firstOrCreate(['name' => 'staff']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $superRole = Role::firstOrCreate(['name' => 'super-admin']);

        if ($flow && !$flow->steps()->exists()) {
            ProcessApproval::createStep($flow->id, $staffRole->id, \RingleSoft\LaravelProcessApproval\Enums\ApprovalTypeEnum::VERIFY);
            ProcessApproval::createStep($flow->id, $adminRole->id);
            ProcessApproval::createStep($flow->id, $superRole->id);
        }
    }
}

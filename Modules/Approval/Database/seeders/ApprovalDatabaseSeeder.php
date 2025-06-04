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

        $role = Role::firstOrCreate(['name' => 'admin']);

        if ($flow && !$flow->steps()->exists()) {
            ProcessApproval::createStep($flow->id, $role->id);
        }
    }
}

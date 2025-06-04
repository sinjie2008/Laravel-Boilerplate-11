<?php

namespace Modules\FundRequest\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\FundRequest\App\Models\FundRequest;
use RingleSoft\LaravelProcessApproval\Enums\ApprovalTypeEnum;
use Spatie\Permission\Models\Role;

class FundRequestApprovalSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::where('name', 'admin')->first();
        $staff = Role::where('name', 'staff')->first();
        $super = Role::where('name', 'super-admin')->first();

        if ($admin && $staff && $super) {
            FundRequest::makeApprovable([
                $admin->id => ApprovalTypeEnum::CHECK,
                $staff->id => ApprovalTypeEnum::CHECK,
                $super->id => ApprovalTypeEnum::APPROVE,
            ]);
        }
    }
}

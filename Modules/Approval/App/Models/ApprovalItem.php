<?php

namespace Modules\Approval\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use RingleSoft\LaravelProcessApproval\Contracts\ApprovableModel;
use RingleSoft\LaravelProcessApproval\Traits\Approvable;

class ApprovalItem extends Model implements ApprovableModel
{
    use HasFactory, Approvable;

    protected $fillable = [
        'title',
        'description',
        'user_id',
    ];

    public function bypassApprovalProcess(): bool
    {
        return false;
    }

    public function enableAutoSubmit(): bool
    {
        return true;
    }

    public static function getApprovableType(): string
    {
        return self::class;
    }

    public static function approvalFlow(): ?\RingleSoft\LaravelProcessApproval\Models\ProcessApprovalFlow
    {
        return \RingleSoft\LaravelProcessApproval\Models\ProcessApprovalFlow::query()
            ->where('approvable_type', static::class)
            ->first();
    }

    public function onApprovalCompleted(\RingleSoft\LaravelProcessApproval\Models\ProcessApproval $approval): bool
    {
        // Called when approval is completed; return true to finalize
        return true;
    }
}

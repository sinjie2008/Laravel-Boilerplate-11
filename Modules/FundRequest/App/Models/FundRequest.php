<?php

namespace Modules\FundRequest\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RingleSoft\LaravelProcessApproval\Contracts\ApprovableModel;
use RingleSoft\LaravelProcessApproval\Traits\Approvable;
use RingleSoft\LaravelProcessApproval\Models\ProcessApproval;

class FundRequest extends Model implements ApprovableModel
{
    use HasFactory, Approvable;

    protected $fillable = [
        'user_id',
        'amount',
        'description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function onApprovalCompleted(ProcessApproval $approval): bool
    {
        // Logic after approval can be placed here
        return true;
    }
}

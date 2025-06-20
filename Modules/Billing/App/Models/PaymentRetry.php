<?php

declare(strict_types=1);

namespace Modules\Billing\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentRetry extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'status',
        'failure_reason',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}

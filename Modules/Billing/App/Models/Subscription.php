<?php

declare(strict_types=1);

namespace Modules\Billing\App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Cashier\Subscription as CashierSubscription;

class Subscription extends CashierSubscription
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'stripe_price', 'stripe_id');
    }

    public function paymentRetries(): HasMany
    {
        return $this->hasMany(PaymentRetry::class);
    }
}

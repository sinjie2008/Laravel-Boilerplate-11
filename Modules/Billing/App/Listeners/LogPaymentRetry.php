<?php

declare(strict_types=1);

namespace Modules\Billing\App\Listeners;

use Modules\Billing\App\Events\PaymentRetried;

class LogPaymentRetry
{
    public function handle(PaymentRetried $event): void
    {
        activity()
            ->performedOn($event->retry->subscription)
            ->causedBy(auth()->user())
            ->withProperties(['status' => $event->retry->status])
            ->log('retried payment');
    }
}

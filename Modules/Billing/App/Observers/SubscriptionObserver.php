<?php

declare(strict_types=1);

namespace Modules\Billing\App\Observers;

use Modules\Billing\App\Models\Subscription;

class SubscriptionObserver
{
    public function created(Subscription $subscription): void
    {
        activity()
            ->performedOn($subscription)
            ->causedBy(auth()->user())
            ->log('created subscription');
    }

    public function updated(Subscription $subscription): void
    {
        activity()
            ->performedOn($subscription)
            ->causedBy(auth()->user())
            ->log('updated subscription');
    }

    public function deleted(Subscription $subscription): void
    {
        activity()
            ->performedOn($subscription)
            ->causedBy(auth()->user())
            ->log('deleted subscription');
    }
}

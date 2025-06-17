<?php

declare(strict_types=1);

namespace Modules\BillingManager\Services;

use App\Models\User;
use Laravel\Cashier\Cashier;

class SubscriptionService
{
    public function subscribe(User $user, string $priceId, ?string $paymentMethod = null): void
    {
        $builder = $user->newSubscription('default', $priceId);

        if ($paymentMethod) {
            $builder->create($paymentMethod);
        } else {
            $builder->create();
        }
    }

    public function cancel(User $user): void
    {
        $user->subscription('default')?->cancel();
    }
}

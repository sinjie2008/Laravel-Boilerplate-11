<?php

declare(strict_types=1);

namespace Modules\BillingManager\Services;

use App\Models\User;

class PaymentMethodService
{
    public function add(User $user, string $paymentMethodId): void
    {
        $user->addPaymentMethod($paymentMethodId);
        $user->updateDefaultPaymentMethod($paymentMethodId);
    }
}

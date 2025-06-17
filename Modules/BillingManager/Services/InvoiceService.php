<?php

declare(strict_types=1);

namespace Modules\BillingManager\Services;

use App\Models\User;
use Laravel\Cashier\Cashier;

class InvoiceService
{
    public function find(User $user, string $invoiceId)
    {
        return $user->findInvoice($invoiceId);
    }

    public function refund(string $invoiceId): void
    {
        Cashier::stripe()->refunds->create([
            'invoice' => $invoiceId,
        ]);
    }
}

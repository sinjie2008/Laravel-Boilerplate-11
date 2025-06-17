<?php

declare(strict_types=1);

namespace Modules\BillingManager\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\BillingManager\Services\InvoiceService;

class RefundController extends Controller
{
    public function __construct(private InvoiceService $invoices)
    {
    }

    public function store(string $invoice): RedirectResponse
    {
        $this->invoices->refund($invoice);

        return redirect()->route('admin.billing.index');
    }
}

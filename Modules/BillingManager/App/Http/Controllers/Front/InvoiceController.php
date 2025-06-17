<?php

declare(strict_types=1);

namespace Modules\BillingManager\App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Modules\BillingManager\Services\InvoiceService;

class InvoiceController extends Controller
{
    public function __construct(private InvoiceService $invoices)
    {
    }

    public function __invoke(string $invoice): Response
    {
        $invoiceData = $this->invoices->find(auth()->user(), $invoice);

        return response()->view('billing::front.invoice', [
            'invoice' => $invoiceData,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace Modules\BillingManager\App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class InvoiceController extends Controller
{
    public function __invoke(string $invoice): Response
    {
        return response()->view('billing::front.invoice', ['invoice' => $invoice]);
    }
}

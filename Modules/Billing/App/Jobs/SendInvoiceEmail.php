<?php

declare(strict_types=1);

namespace Modules\Billing\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Modules\Billing\App\Mail\InvoiceCreated;
use Modules\Billing\App\Models\Invoice;

class SendInvoiceEmail implements ShouldQueue
{
    use Queueable;

    public function __construct(public Invoice $invoice) {}

    public function handle(): void
    {
        Mail::to($this->invoice->user->email)->queue(new InvoiceCreated($this->invoice));
    }
}

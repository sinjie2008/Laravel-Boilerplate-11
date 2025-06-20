<?php

namespace Modules\Billing\App\Mail;

use Illuminate\Mail\Mailable;
use Modules\Billing\App\Models\Invoice;

class InvoiceCreated extends Mailable
{
    public function __construct(public Invoice $invoice)
    {
        $this->subject('Invoice Created');
    }

    public function build(): self
    {
        return $this->view('billing::emails.invoice')->with(['invoice' => $this->invoice]);
    }
}

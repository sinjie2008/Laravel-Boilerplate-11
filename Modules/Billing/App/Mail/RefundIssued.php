<?php

namespace Modules\Billing\App\Mail;

use Illuminate\Mail\Mailable;
use Modules\Billing\App\Models\Invoice;

class RefundIssued extends Mailable
{
    public function __construct(public Invoice $invoice)
    {
        $this->subject('Refund Issued');
    }

    public function build(): self
    {
        return $this->view('billing::emails.refund')->with(['invoice' => $this->invoice]);
    }
}

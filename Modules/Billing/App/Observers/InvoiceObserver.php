<?php

namespace Modules\Billing\App\Observers;

use Modules\Billing\App\Models\Invoice;

class InvoiceObserver
{
    /**
     * Handle the Invoice "created" event.
     */
    public function created(Invoice $invoice): void
    {
        activity()
            ->performedOn($invoice)
            ->causedBy(auth()->user())
            ->log('created invoice');
    }

    /**
     * Handle the Invoice "updated" event.
     */
    public function updated(Invoice $invoice): void
    {
        activity()
            ->performedOn($invoice)
            ->causedBy(auth()->user())
            ->log('updated invoice');
    }

    /**
     * Handle the Invoice "deleted" event.
     */
    public function deleted(Invoice $invoice): void
    {
        activity()
            ->performedOn($invoice)
            ->causedBy(auth()->user())
            ->log('deleted invoice');
    }

    /**
     * Handle the Invoice "restored" event.
     */
    public function restored(Invoice $invoice): void
    {
        //
    }

    /**
     * Handle the Invoice "force deleted" event.
     */
    public function forceDeleted(Invoice $invoice): void
    {
        //
    }
}

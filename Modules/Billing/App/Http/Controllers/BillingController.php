<?php

namespace Modules\Billing\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\Billing\App\Http\Requests\RefundInvoiceRequest;
use Modules\Billing\App\Http\Requests\StoreInvoiceRequest;
use Modules\Billing\App\Http\Requests\UpdateInvoiceRequest;
use Modules\Billing\App\Jobs\ProcessRefund;
use Modules\Billing\App\Jobs\SendInvoiceEmail;
use Modules\Billing\App\Models\Invoice;
use Modules\Role\App\Models\User;

class BillingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = Invoice::with('user')->latest()->get();

        $mrr = Invoice::where('status', 'paid')
            ->whereBetween('due_date', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('amount');

        $churnCount = Invoice::where('status', 'refunded')
            ->where('updated_at', '>=', now()->subDays(30))
            ->count();
        $totalInvoices = Invoice::count();
        $churnRate = $totalInvoices ? ($churnCount / $totalInvoices) * 100 : 0;

        $pastDueCount = Invoice::where('status', '!=', 'paid')
            ->whereDate('due_date', '<', now())
            ->count();

        $revenueData = Invoice::where('status', 'paid')
            ->where('updated_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(updated_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date');

        return view('billing::index', compact('invoices', 'mrr', 'churnRate', 'pastDueCount', 'revenueData'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();

        return view('billing::create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvoiceRequest $request): RedirectResponse
    {
        $invoice = Invoice::create($request->validated());
        SendInvoiceEmail::dispatch($invoice);

        return redirect()->route('admin.billing.index')->with('success', 'Invoice created');
    }

    /**
     * Show the specified resource.
     */
    public function show(Invoice $invoice)
    {
        return view('billing::show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        $users = User::all();

        return view('billing::edit', compact('invoice', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        $invoice->update($request->validated());
        if ($invoice->status === 'refunded') {
            ProcessRefund::dispatch($invoice);
        }

        return redirect()->route('admin.billing.index')->with('success', 'Invoice updated');
    }

    public function refund(RefundInvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        ProcessRefund::dispatch($invoice, $request->float('amount'));

        return back()->with('success', 'Refund queued');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice): RedirectResponse
    {
        $invoice->delete();

        return redirect()->route('admin.billing.index')->with('success', 'Invoice deleted');
    }
}

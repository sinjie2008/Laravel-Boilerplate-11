<?php

declare(strict_types=1);

namespace Modules\BillingManager\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class RefundController extends Controller
{
    public function store(string $invoice): RedirectResponse
    {
        return redirect()->route('admin.billing.index');
    }
}

<?php

declare(strict_types=1);

namespace Modules\BillingManager\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index(): Response
    {
        return response()->view('billing::admin.plans.index');
    }

    public function create(): Response
    {
        return response()->view('billing::admin.plans.create');
    }

    public function store(Request $request): RedirectResponse
    {
        return redirect()->route('admin.billing.plans.index');
    }

    public function edit(int $id): Response
    {
        return response()->view('billing::admin.plans.edit', ['id' => $id]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        return redirect()->route('admin.billing.plans.index');
    }

    public function destroy(int $id): RedirectResponse
    {
        return redirect()->route('admin.billing.plans.index');
    }
}

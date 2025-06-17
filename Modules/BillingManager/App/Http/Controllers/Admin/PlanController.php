<?php

declare(strict_types=1);

namespace Modules\BillingManager\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\BillingManager\App\Models\Plan;

class PlanController extends Controller
{
    public function index(): Response
    {
        $plans = Plan::all();

        return response()->view('billing::admin.plans.index', compact('plans'));
    }

    public function create(): Response
    {
        return response()->view('billing::admin.plans.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'slug' => 'required|string|unique:plans,slug',
            'price' => 'required|integer',
            'currency' => 'required|string|size:3',
            'stripe_price_id' => 'required|string',
        ]);

        Plan::create($validated);

        return redirect()->route('admin.billing.plans.index');
    }

    public function edit(Plan $plan): Response
    {
        return response()->view('billing::admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'slug' => 'required|string|unique:plans,slug,'.$plan->id,
            'price' => 'required|integer',
            'currency' => 'required|string|size:3',
            'stripe_price_id' => 'required|string',
        ]);

        $plan->update($validated);

        return redirect()->route('admin.billing.plans.index');
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        $plan->delete();

        return redirect()->route('admin.billing.plans.index');
    }
}

<?php

declare(strict_types=1);

namespace Modules\Billing\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\Billing\App\Http\Requests\StorePlanRequest;
use Modules\Billing\App\Http\Requests\UpdatePlanRequest;
use Modules\Billing\App\Models\Plan;
use Modules\Billing\App\Services\StripeService;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::all();

        return view('billing::plans.index', compact('plans'));
    }

    public function create()
    {
        return view('billing::plans.create');
    }

    public function store(StorePlanRequest $request, StripeService $stripe): RedirectResponse
    {
        $data = $request->validated();
        try {
            $stripeId = $stripe->createPlan($data);
            $data['stripe_id'] = $stripeId;
            $data['synced'] = true;
        } catch (\Exception $e) {
            $data['synced'] = false;
        }
        Plan::create($data);

        return redirect()->route('admin.plans.index')->with('success', 'Plan created');
    }

    public function edit(Plan $plan)
    {
        return view('billing::plans.edit', compact('plan'));
    }

    public function update(UpdatePlanRequest $request, Plan $plan, StripeService $stripe): RedirectResponse
    {
        $data = $request->validated();
        try {
            $stripe->updatePlan($plan, $data);
            $data['synced'] = true;
        } catch (\Exception $e) {
            $data['synced'] = false;
        }
        $plan->update($data);

        return redirect()->route('admin.plans.index')->with('success', 'Plan updated');
    }

    public function destroy(Plan $plan, StripeService $stripe): RedirectResponse
    {
        try {
            $stripe->deletePlan($plan);
        } catch (\Exception $e) {
            // ignore
        }
        $plan->delete();

        return redirect()->route('admin.plans.index')->with('success', 'Plan deleted');
    }
}

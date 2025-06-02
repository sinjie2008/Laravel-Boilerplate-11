<?php

namespace Modules\SubscriptionManager\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\SubscriptionManager\Models\Plan;
use Spatie\Permission\Models\Permission; // Added

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $plans = Plan::with('permissions')->latest()->paginate(10); // Eager load permissions
        return view('subscriptionmanager::admin.plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $permissions = Permission::orderBy('name')->get();
        return view('subscriptionmanager::admin.plans.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:plans,name',
            'price' => 'required|numeric|min:0',
            'api_call_limit_per_day' => 'required|integer|min:0',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:'.config('permission.table_names.permissions').',id',
        ]);

        $plan = Plan::create($request->only(['name', 'price', 'api_call_limit_per_day']));
        $plan->permissions()->sync($request->input('permissions', []));

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plan created successfully with associated permissions.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Plan $plan): View
    {
        $plan->load('permissions'); // Eager load permissions
        return view('subscriptionmanager::admin.plans.show', compact('plan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Plan $plan): View
    {
        $permissions = Permission::orderBy('name')->get();
        $planPermissions = $plan->permissions->pluck('id')->toArray();
        return view('subscriptionmanager::admin.plans.edit', compact('plan', 'permissions', 'planPermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:plans,name,' . $plan->id,
            'price' => 'required|numeric|min:0',
            'api_call_limit_per_day' => 'required|integer|min:0',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:'.config('permission.table_names.permissions').',id',
        ]);

        $plan->update($request->only(['name', 'price', 'api_call_limit_per_day']));
        $plan->permissions()->sync($request->input('permissions', []));

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plan updated successfully with associated permissions.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Plan $plan): RedirectResponse
    {
        // Permissions will be detached automatically due to onDelete('cascade')
        // or by the sync([]) if we were to do it manually before delete.
        // However, Spatie's default setup might not use onDelete('cascade') on the pivot.
        // It's safer to detach them explicitly if not relying on DB cascade.
        $plan->permissions()->detach();
        $plan->delete();

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plan deleted successfully.');
    }
}

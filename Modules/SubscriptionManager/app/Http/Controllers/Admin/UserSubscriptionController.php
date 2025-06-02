<?php

namespace Modules\SubscriptionManager\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\SubscriptionManager\Models\Subscription;
use Modules\SubscriptionManager\Models\Plan;
use Modules\Role\App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UserSubscriptionController extends Controller
{
    protected function syncUserPermissions(User $user, ?Plan $plan): void
    {
        // Revoke all permissions previously granted by any plan from this module
        // This is a simplistic approach; a more robust system might tag plan-specific permissions
        $allModulePlanPermissions = Plan::with('permissions')->get()->flatMap->permissions->pluck('name')->unique()->toArray();
        $user->revokePermissionTo($allModulePlanPermissions);

        if ($plan) {
            $user->givePermissionTo($plan->permissions->pluck('name')->toArray());
        }
    }

    protected function manageApiToken(User $user, Subscription $subscription, bool $revokeExisting = true): ?string
    {
        $tokenName = 'subscription-token-' . $subscription->id;

        if ($revokeExisting) {
            $user->tokens()->where('name', $tokenName)->delete();
        }

        if (in_array($subscription->status, ['active'])) {
            // Determine abilities based on plan's permissions
            $abilities = $subscription->plan ? $subscription->plan->permissions->pluck('name')->toArray() : [];
            $plainTextToken = $user->createToken($tokenName, $abilities)->plainTextToken;
            // Store a reference or part of the token if needed for admin display, but NOT the plainTextToken itself.
            // For this example, we'll just return it for the success message, but it won't be stored directly.
            return $plainTextToken;
        }
        return null;
    }

    public function index(): View
    {
        $subscriptions = Subscription::with(['user', 'plan'])->latest()->paginate(10);
        return view('subscriptionmanager::admin.subscriptions.index', compact('subscriptions'));
    }

    public function create(): View
    {
        $users = User::orderBy('name')->get();
        $plans = Plan::orderBy('name')->get();
        $statuses = ['pending' => 'Pending', 'active' => 'Active', 'expired' => 'Expired', 'cancelled' => 'Cancelled'];
        return view('subscriptionmanager::admin.subscriptions.create', compact('users', 'plans', 'statuses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'status' => 'required|string|in:pending,active,expired,cancelled',
        ]);

        $user = User::findOrFail($request->user_id);
        $plan = Plan::findOrFail($request->plan_id);

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'starts_at' => $request->starts_at ? Carbon::parse($request->starts_at) : Carbon::now(),
            'ends_at' => $request->ends_at ? Carbon::parse($request->ends_at) : null,
            'status' => $request->status,
        ]);

        $this->syncUserPermissions($user, $subscription->status === 'active' ? $plan : null);
        $tokenInfo = $this->manageApiToken($user, $subscription);

        $message = 'Subscription created successfully.';
        if ($tokenInfo) {
            $message .= " API Token generated: " . $tokenInfo;
        }

        return redirect()->route('admin.subscriptions.index')->with('success', $message);
    }

    public function show(Subscription $subscription): View
    {
        $subscription->load(['user', 'plan.permissions']);
        // For admin reference, show token names, not actual tokens
        $tokenName = 'subscription-token-' . $subscription->id;
        $apiTokenExists = $subscription->user->tokens()->where('name', $tokenName)->exists();

        $token = null;
        if (session('success')) {
            $successMessage = session('success');
            $tokenPrefix = 'API Token generated: ';
            if (str_contains($successMessage, $tokenPrefix)) {
                $token = substr($successMessage, strpos($successMessage, $tokenPrefix) + strlen($tokenPrefix));
            }
        }

        $isSuperAdmin = Auth::user() ? Auth::user()->hasRole('superadmin') : false;

        return view('subscriptionmanager::admin.subscriptions.show', compact('subscription', 'apiTokenExists', 'tokenName', 'token', 'isSuperAdmin'));
    }

    public function edit(Subscription $subscription): View
    {
        $users = User::orderBy('name')->get();
        $plans = Plan::orderBy('name')->get();
        $statuses = ['pending' => 'Pending', 'active' => 'Active', 'expired' => 'Expired', 'cancelled' => 'Cancelled'];
        $subscription->load(['user', 'plan']);
        return view('subscriptionmanager::admin.subscriptions.edit', compact('subscription', 'users', 'plans', 'statuses'));
    }

    public function update(Request $request, Subscription $subscription): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id', // Usually not changed, but good to have
            'plan_id' => 'required|exists:plans,id',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'status' => 'required|string|in:pending,active,expired,cancelled',
        ]);

        $user = User::findOrFail($request->user_id);
        $plan = Plan::findOrFail($request->plan_id);
        $originalStatus = $subscription->status;

        $subscription->update([
            'user_id' => $user->id, // In case user can be changed
            'plan_id' => $plan->id,
            'starts_at' => $request->starts_at ? Carbon::parse($request->starts_at) : $subscription->starts_at,
            'ends_at' => $request->ends_at ? Carbon::parse($request->ends_at) : null,
            'status' => $request->status,
        ]);
        
        // Refresh subscription to get updated plan relationship if it changed
        $subscription->refresh()->load('plan');

        $this->syncUserPermissions($user, $subscription->status === 'active' ? $subscription->plan : null);
        
        // Manage token: revoke if status is no longer active, or if plan/user changed. Create if now active.
        $revokeToken = $originalStatus === 'active' && $subscription->status !== 'active';
        $revokeToken = $revokeToken || ($request->plan_id != $subscription->getOriginal('plan_id'));
        $revokeToken = $revokeToken || ($request->user_id != $subscription->getOriginal('user_id'));

        $this->manageApiToken($user, $subscription, $revokeToken);


        return redirect()->route('admin.subscriptions.index')->with('success', 'Subscription updated successfully.');
    }

    public function destroy(Subscription $subscription): RedirectResponse
    {
        $user = $subscription->user;
        $this->manageApiToken($user, $subscription, true); // Revoke token
        $this->syncUserPermissions($user, null); // Revoke plan permissions

        $subscription->delete();

        return redirect()->route('admin.subscriptions.index')->with('success', 'Subscription deleted successfully.');
    }

    // Optional: Method to regenerate a token for an existing active subscription
    public function regenerateToken(Subscription $subscription): RedirectResponse
    {
        $user = Auth::user();
        $isSuperAdmin = $user ? $user->hasRole('superadmin') : false;

        if ($subscription->status !== 'active' && !$isSuperAdmin) {
            return redirect()->route('admin.subscriptions.show', $subscription)->with('error', 'Cannot regenerate token for inactive subscription unless you are a superadmin.');
        }
        
        $targetUser = $subscription->user;
        $this->manageApiToken($targetUser, $subscription, true); // Revoke existing and create new
        
        return redirect()->route('admin.subscriptions.show', $subscription)->with('success', 'API Token regenerated successfully.');
    }
}

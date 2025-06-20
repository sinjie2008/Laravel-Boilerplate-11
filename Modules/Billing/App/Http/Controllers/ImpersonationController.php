<?php

declare(strict_types=1);

namespace Modules\Billing\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Modules\Role\App\Models\User;

class ImpersonationController extends Controller
{
    public function start(User $user): RedirectResponse
    {
        $admin = Auth::user();
        session(['impersonator_id' => $admin->id]);
        Auth::login($user);

        activity()->causedBy($admin)->performedOn($user)->log('started impersonation');

        return redirect('/')
            ->with('success', 'Now impersonating '.$user->name);
    }

    public function stop(): RedirectResponse
    {
        $adminId = session('impersonator_id');

        if ($adminId) {
            $admin = User::find($adminId);
            Auth::login($admin);
            session()->forget('impersonator_id');

            activity()->causedBy($admin)->log('stopped impersonation');
        }

        return redirect('/admin')
            ->with('success', 'Impersonation ended');
    }
}

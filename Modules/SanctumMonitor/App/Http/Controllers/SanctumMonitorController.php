<?php

declare(strict_types=1);

namespace Modules\SanctumMonitor\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;
use Modules\SanctumMonitor\App\Models\ApiActivity;
use Modules\SanctumMonitor\App\Models\TokenAuditLog;

class SanctumMonitorController extends Controller
{
    public function index()
    {
        return view('sanctummonitor::dashboard');
    }

    public function tokens()
    {
        $tokens = PersonalAccessToken::with('tokenable')->latest()->get();

        return view('sanctummonitor::tokens', compact('tokens'));
    }

    public function revoke(int $tokenId): RedirectResponse
    {
        $token = PersonalAccessToken::findOrFail($tokenId);
        $token->delete();

        TokenAuditLog::create([
            'tokenable_id' => $token->tokenable_id,
            'tokenable_type' => $token->tokenable_type,
            'name' => $token->name,
            'action' => 'revoked',
            'ip_address' => request()->ip(),
            'user_id' => auth()->id(),
        ]);

        return redirect()->back();
    }

    public function activity()
    {
        $activities = ApiActivity::latest()->paginate(50);

        return view('sanctummonitor::activity', compact('activities'));
    }

    public function stats()
    {
        $totalRequests = ApiActivity::count();
        $topRoutes = ApiActivity::select('route', DB::raw('count(*) as total'))
            ->groupBy('route')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
        $topUsers = ApiActivity::select('user_id', DB::raw('count(*) as total'))
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('sanctummonitor::stats', compact('totalRequests', 'topRoutes', 'topUsers'));
    }

    public function logs()
    {
        $logs = TokenAuditLog::latest()->paginate(50);

        return view('sanctummonitor::logs', compact('logs'));
    }

    public function settings(Request $request)
    {
        if ($request->isMethod('post')) {
            config(['sanctummonitor.log_retention_days' => (int) $request->input('log_retention_days')]);
            config(['sanctummonitor.enable_logging' => (bool) $request->input('enable_logging')]);

            return redirect()->back();
        }

        return view('sanctummonitor::settings', [
            'log_retention_days' => config('sanctummonitor.log_retention_days'),
            'enable_logging' => config('sanctummonitor.enable_logging'),
        ]);
    }
}

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
use Yajra\DataTables\Facades\DataTables;

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

    public function tokensData()
    {
        $tokens = PersonalAccessToken::with('tokenable')->select('personal_access_tokens.*');

        return DataTables::of($tokens)
            ->addColumn('user_name', function (PersonalAccessToken $token) {
                return optional($token->tokenable)->name;
            })
            ->addColumn('ip', function (PersonalAccessToken $token) {
                return $token->ip;
            })
            ->addColumn('abilities_list', function (PersonalAccessToken $token) {
                return implode(', ', $token->abilities ?? []);
            })
            ->addColumn('action', function (PersonalAccessToken $token) {
                $route = route('admin.sanctummonitor.tokens.revoke', $token->id);
                return '<form action="'.$route.'" method="POST" onsubmit="return confirm(\'Are you sure you want to revoke this token?\');">
                            '.csrf_field().'
                            '.method_field('DELETE').'
                            <button type="submit" class="btn btn-danger btn-sm">Revoke</button>
                        </form>';
            })
            ->rawColumns(['action'])
            ->make(true);
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

    public function activityData()
    {
        try {
            $activities = ApiActivity::with('user')->select('api_activities.*');

            return DataTables::of($activities)
                ->addColumn('user_name', function (ApiActivity $activity) {
                    return optional($activity->user)->name;
                })
                ->rawColumns(['user_name'])
                ->make(true);
        } catch (\Exception $e) {
            \Log::error('DataTables AJAX Error in activityData: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'An error occurred while fetching data.'], 500);
        }
    }

    public function stats()
    {
        $totalRequests = ApiActivity::count();
        $topRoutes = ApiActivity::select('route', DB::raw('count(*) as total'))
            ->groupBy('route')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
        $topUsers = ApiActivity::with('user') // Eager load the user relationship
            ->select('user_id', DB::raw('count(*) as total'))
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

    public function logsData()
    {
        $logs = TokenAuditLog::with('user')->select('token_audit_logs.*');

        return DataTables::of($logs)
            ->addColumn('user_name', function (TokenAuditLog $log) {
                return optional($log->user)->name;
            })
            ->rawColumns(['user_name'])
            ->make(true);
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

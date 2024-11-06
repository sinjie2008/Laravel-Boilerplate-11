<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\DB;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view activity logs');
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $activities = DB::table('activity_log')
            ->leftJoin('users', 'activity_log.causer_id', '=', 'users.id')
            ->select('activity_log.*', 'users.name as causer_name')
            ->when($search, function ($query) use ($search) {
                return $query->where('activity_log.description', 'like', "%{$search}%")
                             ->orWhere('users.name', 'like', "%{$search}%");
            })
            ->orderBy('activity_log.created_at', 'DESC')
            ->paginate(10);
        
        return view('activity-logs.index', compact('activities', 'search'));
    }
} 
<?php

namespace Modules\ActivityLog\App\Http\Controllers; // Updated namespace

use App\Http\Controllers\Controller; // Keep using the base Controller
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\DB;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        // The permission middleware will be handled by the route definition later
        // $this->middleware('permission:view activity logs'); 
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
        
        // Updated view path to use the module's namespace
        return view('activitylog::index', compact('activities', 'search')); 
    }
}

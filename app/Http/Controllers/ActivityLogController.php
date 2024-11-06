<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view activity logs');
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $activities = Activity::with('causer', 'subject')
            ->when($search, function ($query) use ($search) {
                return $query->where('description', 'like', "%{$search}%")
                            ->orWhereHas('causer', function($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%");
                            });
            })
            ->latest()
            ->paginate(10);
            
        return view('activity-logs.index', compact('activities', 'search'));
    }
} 
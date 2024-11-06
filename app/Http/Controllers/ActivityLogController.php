<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view activity logs');
    }

    public function index()
    {
        $activities = Activity::with('causer', 'subject')
            ->latest()
            ->paginate(10);
            
        return view('activity-logs.index', compact('activities'));
    }
} 
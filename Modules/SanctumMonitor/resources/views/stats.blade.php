@extends('sanctummonitor::layouts.master')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-xl font-bold mb-4">API Statistics</h2>
    <p>Total Requests: {{ $totalRequests }}</p>
    <h3 class="font-bold mt-4">Top Routes</h3>
    <ul>
        @foreach($topRoutes as $route)
            <li>{{ $route->route }} - {{ $route->total }}</li>
        @endforeach
    </ul>
    <h3 class="font-bold mt-4">Most Active Users</h3>
    <ul>
        @foreach($topUsers as $user)
            <li>User {{ $user->user_id }} - {{ $user->total }}</li>
        @endforeach
    </ul>
</div>
@endsection

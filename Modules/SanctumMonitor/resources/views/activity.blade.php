@extends('sanctummonitor::layouts.master')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-xl font-bold mb-4">Activity</h2>
    <table class="table-auto w-full">
        <thead>
            <tr>
                <th>User</th>
                <th>Route</th>
                <th>IP</th>
                <th>Method</th>
                <th>User Agent</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            @foreach($activities as $activity)
            <tr>
                <td>{{ optional($activity->user)->name }}</td>
                <td>{{ $activity->route }}</td>
                <td>{{ $activity->ip_address }}</td>
                <td>{{ $activity->method }}</td>
                <td>{{ $activity->user_agent }}</td>
                <td>{{ $activity->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $activities->links() }}
</div>
@endsection

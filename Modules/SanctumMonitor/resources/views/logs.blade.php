@extends('sanctummonitor::layouts.master')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-xl font-bold mb-4">Token Audit Logs</h2>
    <table class="table-auto w-full">
        <thead>
            <tr>
                <th>Tokenable</th>
                <th>Name</th>
                <th>Action</th>
                <th>IP</th>
                <th>User</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
            <tr>
                <td>{{ $log->tokenable_type }}#{{ $log->tokenable_id }}</td>
                <td>{{ $log->name }}</td>
                <td>{{ $log->action }}</td>
                <td>{{ $log->ip_address }}</td>
                <td>{{ $log->user_id }}</td>
                <td>{{ $log->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $logs->links() }}
</div>
@endsection

@extends('sanctummonitor::layouts.master')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Sanctum Monitor</h1>
    <ul class="space-x-4">
        <li><a href="{{ route('admin.sanctummonitor.tokens') }}">Tokens</a></li>
        <li><a href="{{ route('admin.sanctummonitor.activity') }}">Activity</a></li>
        <li><a href="{{ route('admin.sanctummonitor.stats') }}">Stats</a></li>
        <li><a href="{{ route('admin.sanctummonitor.logs') }}">Logs</a></li>
        <li><a href="{{ route('admin.sanctummonitor.settings') }}">Settings</a></li>
    </ul>
</div>
@endsection

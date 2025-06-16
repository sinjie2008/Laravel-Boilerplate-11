@extends('adminlte::page')

@section('content_header')
    <h1>Sanctum Monitor Dashboard</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Sanctum Monitor Dashboard</h3>
        </div>
        <div class="card-body">
            <ul class="space-x-4">
        <li><a href="{{ route('admin.sanctummonitor.tokens') }}">Tokens</a></li>
        <li><a href="{{ route('admin.sanctummonitor.activity') }}">Activity</a></li>
        <li><a href="{{ route('admin.sanctummonitor.stats') }}">Stats</a></li>
        <li><a href="{{ route('admin.sanctummonitor.logs') }}">Logs</a></li>
        <li><a href="{{ route('admin.sanctummonitor.settings') }}">Settings</a></li>
    </ul>
</div>
@endsection

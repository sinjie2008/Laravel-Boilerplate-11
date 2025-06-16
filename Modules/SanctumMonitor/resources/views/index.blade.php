@extends('adminlte::page')

@section('content_header')
    <h1>Sanctum Monitor</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Sanctum Monitor</h3>
        </div>
        <div class="card-body">
            <p>Welcome to the Sanctum Monitor module.</p>
            <a href="{{ route('admin.sanctummonitor.dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
        </div>
    </div>
@stop

@extends('adminlte::page')

@section('content_header')
    <h1>Sanctum Monitor Settings</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Settings</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.sanctummonitor.settings') }}">
                @csrf
                <div class="form-group">
                    <label for="log_retention_days">Log Retention Days</label>
                    <input type="number" name="log_retention_days" id="log_retention_days" class="form-control" value="{{ $log_retention_days }}">
                </div>
                <div class="form-group form-check">
                    <input type="checkbox" name="enable_logging" id="enable_logging" class="form-check-input" value="1" {{ $enable_logging ? 'checked' : '' }}>
                    <label class="form-check-label" for="enable_logging">Enable Logging</label>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>
@stop

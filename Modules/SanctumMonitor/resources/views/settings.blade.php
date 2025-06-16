@extends('sanctummonitor::layouts.master')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-xl font-bold mb-4">Settings</h2>
    <form method="POST" action="{{ route('admin.sanctummonitor.settings') }}">
        @csrf
        <div>
            <label>Log Retention Days</label>
            <input type="number" name="log_retention_days" value="{{ $log_retention_days }}">
        </div>
        <div>
            <label>Enable Logging</label>
            <input type="checkbox" name="enable_logging" value="1" {{ $enable_logging ? 'checked' : '' }}>
        </div>
        <button type="submit">Save</button>
    </form>
</div>
@endsection

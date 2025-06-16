@extends('adminlte::page')

@section('plugins.Datatables', true)

@section('content_header')
    <h1>Sanctum Monitor Activity</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Sanctum Monitor Activity</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="activity-table">
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
            {{-- DataTables will populate this table body via AJAX --}}
        </tbody>
    </table>
        </div>
    </div>
@stop

@section('css')
    {{-- Add custom CSS if needed --}}
@stop

@push('js')
    <script>
        $(document).ready(function() {
            $('#activity-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.sanctummonitor.activity.data') }}',
                columns: [
                    { data: 'user_name', name: 'user_name' },
                    { data: 'route', name: 'route' },
                    { data: 'ip_address', name: 'ip_address' },
                    { data: 'method', name: 'method' },
                    { data: 'user_agent', name: 'user_agent' },
                    { data: 'created_at', name: 'created_at' }
                ]
            });
        });
    </script>
@endpush

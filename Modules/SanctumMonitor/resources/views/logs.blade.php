@extends('adminlte::page')

@section('plugins.Datatables', true)

@section('content_header')
    <h1>Token Audit Logs</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Token Audit Logs</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="logs-table">
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
            $('#logs-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.sanctummonitor.logs.data') }}',
                columns: [
                    { data: 'tokenable_type', name: 'tokenable_type' },
                    { data: 'name', name: 'name' },
                    { data: 'action', name: 'action' },
                    { data: 'ip_address', name: 'ip_address' },
                    { data: 'user_name', name: 'user_name' }, // Use user_name from addColumn
                    { data: 'created_at', name: 'created_at' }
                ]
            });
        });
    </script>
@endpush

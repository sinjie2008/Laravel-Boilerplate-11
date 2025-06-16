@extends('adminlte::page')

@section('plugins.Datatables', true)

@section('content_header')
    <h1>Personal Access Tokens</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Personal Access Tokens</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="tokens-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Name</th>
                        <th>IP</th>
                        <th>Abilities</th>
                        <th>Created</th>
                        <th>Last Used</th>
                        <th>Action</th>
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
            $('#tokens-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.sanctummonitor.tokens.data') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'user_name', name: 'user_name' },
                    { data: 'name', name: 'name' },
                    { data: 'ip', name: 'ip' },
                    { data: 'abilities_list', name: 'abilities_list', orderable: false, searchable: false },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'last_used_at', name: 'last_used_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        });
    </script>
@endpush

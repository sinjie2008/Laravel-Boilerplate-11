@extends('adminlte::page')

@section('plugins.Datatables', true)

@section('content_header')
    <h1>Fund Requests</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Fund Requests</h3>
        </div>
        <div class="card-body">
            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            @endif

            <a href="{{ route('admin.fund-request.create') }}" class="btn btn-success mb-3">Create Fund Request</a>

            <table class="table table-bordered" id="fundrequests-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Purpose</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@stop

@push('js')
    <script>
        $(document).ready(function() {
            $('#fundrequests-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.fund-request.index') }}',
                columns: [
                    { data: 'user.name', name: 'user.name' },
                    { data: 'amount', name: 'amount' },
                    { data: 'purpose', name: 'purpose' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        });
    </script>
@endpush

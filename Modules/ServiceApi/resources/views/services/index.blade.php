@extends('adminlte::page')

@section('plugins.Datatables', true)

@section('title', 'Service List')

@section('content_header')
    <h1>Service List</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Service List</h3>
        </div>
        <div class="card-body">
            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            @endif

            <a href="{{ route('serviceapi.services.create') }}" class="btn btn-success mb-3">Create New Service</a>

            <table class="table table-bordered" id="services-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Table body will be populated by DataTables via AJAX or server-side rendering --}}
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
            $('#services-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('serviceapi.services.index') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'description', name: 'description' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        });
    </script>
@endpush

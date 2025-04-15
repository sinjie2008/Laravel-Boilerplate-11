@extends('adminlte::page')

@section('plugins.Datatables', true) {{-- Ensure DataTables assets are loaded --}}

@section('content_header')
    <h1>Todo List</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Todo List</h3>
        </div>
        <div class="card-body">
            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            @endif

            <a href="{{ route('admin.todolist.create') }}" class="btn btn-success mb-3">Create New Todo</a>

            <table class="table table-bordered" id="todos-table"> {{-- Added ID for DataTables --}}
                <thead> {{-- Added thead --}}
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Completed</th>
                <th>Action</th> {{-- Removed fixed width --}}
            </tr>
        </thead>
                <tbody>
                    {{-- Table body will be populated by DataTables via AJAX --}}
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
            $('#todos-table').DataTable({
                processing: true, // Show processing indicator
                serverSide: true, // Enable server-side processing
                ajax: '{{ route('admin.todolist.index') }}', // URL to fetch data
                columns: [
                    { data: 'title', name: 'title' },
                    { data: 'description', name: 'description' },
                    { data: 'completed', name: 'completed' },
                    { data: 'action', name: 'action', orderable: false, searchable: false } // Action column
                ]
                // Simplified options - Add back if needed after confirming basic functionality
                // paging: true,
                // lengthChange: false,
                // searching: true,
                // ordering: true,
                // info: true,
                // autoWidth: false,
                // responsive: true,
            });
        });
    </script>
@endpush

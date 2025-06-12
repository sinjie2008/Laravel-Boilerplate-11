@extends('adminlte::page')

@section('plugins.Datatables', true)

@section('title', 'Posts')

@section('content_header')
    <h1>Posts</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Posts List</h3>
        </div>
        <div class="card-body">
            <a href="{{ route('admin.posts.create') }}" class="btn btn-primary mb-3">Create Post</a>
            <table class="table table-bordered" id="posts-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $('#posts-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.posts.index') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'title', name: 'title' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        });
    </script>
@endpush

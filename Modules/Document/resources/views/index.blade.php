@extends('adminlte::page')

{{-- Update title if needed --}}
@section('title', 'Documents')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>List Documents</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">

        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">

            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    @can('create documents')
                    {{-- Update route name for the module --}}
                    <a href="{{ route('document.documents.create') }}" class="btn btn-primary">Upload New Document</a>
                    @endcan
                </div>

                <div class="card-body">
                    <table id="documents-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>File</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Ensure $documents variable is passed correctly from the controller --}}
                            @foreach($documents as $document)
                                <tr>
                                    <td>{{ $document->name }}</td>
                                    <td>{{ $document->description }}</td>
                                    <td>
                                        {{-- Ensure model variable and media collection name are correct --}}
                                        @if($document->hasMedia('documents'))
                                            {{-- Ensure getFileUrl() method exists and works --}}
                                            <a href="{{ $document->getFileUrl() }}" target="_blank" class="btn btn-info">
                                                View File
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        @can('update documents')
                                        {{-- Update route name for the module --}}
                                        <a href="{{ route('document.documents.edit', $document) }}" class="btn btn-primary">Edit</a>
                                        @endcan

                                        @can('delete documents')
                                        {{-- Update route name for the module --}}
                                        <form action="{{ route('document.documents.destroy', $document) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    {{-- Add pagination links if needed --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#documents-table').DataTable({
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search documents...",
                }
            });
        });
    </script>
@stop

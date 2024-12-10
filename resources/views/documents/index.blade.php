@extends('adminlte::page')

@section('title', 'Permissions')

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
                    <a href="{{ route('documents.create') }}" class="btn btn-primary">Upload New Document</a>
                    @endcan
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>File</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $document)
                                <tr>
                                    <td>{{ $document->name }}</td>
                                    <td>{{ $document->description }}</td>
                                    <td>
                                        @if($document->hasMedia('documents'))
                                            <a href="{{ $document->getFileUrl() }}" target="_blank" class="btn btn-info">
                                                View File
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        @can('update documents')
                                        <a href="{{ route('documents.edit', $document) }}" class="btn btn-primary">Edit</a>
                                        @endcan

                                        @can('delete documents')
                                        <form action="{{ route('documents.destroy', $document) }}" method="POST" class="d-inline">
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

                </div>
            </div>
        </div>
    </div>
@endsection

@extends('adminlte::page')

@section('title', 'Create Permission')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Edit Permissions</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
        
        </div>
    </div>
@stop

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card">

                <div class="card-body">
                    <form method="POST" action="{{ route('documents.update', $document) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $document->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description', $document->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="document">Document</label>
                            <input type="file" class="form-control @error('document') is-invalid @enderror" id="document" name="document">
                            @error('document')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            @if($document->hasMedia('documents'))
                                <div class="mt-2">
                                    <p>Current file: <a href="{{ $document->getFirstMediaUrl('documents') }}" target="_blank">View File</a></p>
                                </div>
                            @endif
                        </div>

                        <div class="mb-3"">
                            <button type="submit" class="btn btn-primary">Update Document</button>
                            <a href="{{ route('documents.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

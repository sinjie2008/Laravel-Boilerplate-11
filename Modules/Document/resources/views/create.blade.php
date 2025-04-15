@extends('adminlte::page')

@section('title', 'Create Documents')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Create Documents</h1>
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
                    {{-- Update route name for the module --}}
                    <form method="POST" action="{{ route('document.documents.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="name">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="document">Document</label>
                            <input type="file" class="form-control @error('document') is-invalid @enderror" id="document" name="document" required>
                            @error('document')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Upload Document</button>
                            {{-- Update route name for the module --}}
                            <a href="{{ route('document.documents.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

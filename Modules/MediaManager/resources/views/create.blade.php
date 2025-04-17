@extends('adminlte::page')

@section('title', __('Upload New Media'))

@section('content_header')
    <h1 class="m-0 text-dark">{{ __('Upload New Media') }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('mediamanager.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf {{-- CSRF Protection --}}
                    <div class="card-body">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- File Input --}}
                        <div class="form-group">
                            <label for="mediaFile">{{ __('Select File(s)') }}</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    {{-- Allow multiple files using the [] notation and 'multiple' attribute --}}
                                    <input type="file" class="custom-file-input" id="mediaFile" name="mediaFile[]" multiple>
                                    <label class="custom-file-label" for="mediaFile">{{ __('Choose file') }}</label>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                {{ __('You can upload multiple files at once.') }}
                            </small>
                        </div>

                        {{-- Optional: Collection Name (if you want to organize uploads) --}}
                        {{-- <div class="form-group">
                            <label for="collectionName">{{ __('Collection Name (Optional)') }}</label>
                            <input type="text" class="form-control" id="collectionName" name="collection_name" placeholder="e.g., documents, images">
                        </div> --}}

                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">{{ __('Upload') }}</button>
                        <a href="{{ route('mediamanager.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@push('js')
<script>
    // Display selected file names in the custom file input
    $(document).ready(function () {
        bsCustomFileInput.init();
    });
</script>
@endpush 
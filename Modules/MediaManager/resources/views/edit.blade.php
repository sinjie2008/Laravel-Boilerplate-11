@extends('adminlte::page')

@section('title', __('Edit Media'))

@section('content_header')
    <h1 class="m-0 text-dark">{{ __('Edit Media') }}: {{ $media->name }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                {{-- Use route model binding: route('mediamanager.update', $media) --}}
                <form action="{{ route('mediamanager.update', $media) }}" method="POST">
                    @csrf {{-- CSRF Protection --}}
                    @method('PUT') {{-- Specify PUT method for update --}}

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

                        {{-- Media Name Input --}}
                        <div class="form-group">
                            <label for="mediaName">{{ __('Media Name') }}</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="mediaName" name="name" 
                                   value="{{ old('name', $media->name) }}" 
                                   required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        {{-- Display Thumbnail/Icon --}}
                        <div class="form-group">
                            <label>{{ __('Preview') }}</label>
                            <div>
                            @if (Str::startsWith($media->mime_type, 'image/'))
                                <img src="{{ $media->getFullUrl('thumb') }}" alt="{{ $media->name }}" style="max-height: 100px; max-width: 150px; object-fit: cover; border: 1px solid #ced4da; padding: 5px; border-radius: .25rem;">
                            @else
                                <span class="text-muted"><i class="fas fa-file fa-3x"></i> {{ $media->mime_type }}</span>
                            @endif
                            </div>
                        </div>

                        {{-- Example: Custom Property Input (e.g., Alt Text) --}}
                        {{-- Uncomment and adapt if you use custom properties --}}
                        {{-- <div class="form-group">
                            <label for="altText">{{ __('Alt Text (Optional)') }}</label>
                            <input type="text" class="form-control @error('custom_properties.alt_text') is-invalid @enderror" 
                                   id="altText" name="custom_properties[alt_text]" 
                                   value="{{ old('custom_properties.alt_text', $media->getCustomProperty('alt_text')) }}">
                            @error('custom_properties.alt_text')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div> --}}
                        
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">{{ __('Update Media') }}</button>
                        <a href="{{ route('mediamanager.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop 
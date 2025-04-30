@extends('adminlte::page')

@section('title', 'LatexManager Configuration')

@section('content_header')
    <h1>LatexManager Configuration</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">PDFLaTeX Path Setting</h3>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('admin.latex-manager.config.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="pdflatex_path">Path to pdflatex executable</label>
                    <input type="text"
                           name="pdflatex_path"
                           id="pdflatex_path"
                           class="form-control @error('pdflatex_path') is-invalid @enderror"
                           value="{{ old('pdflatex_path', $settings->pdflatex_path ?? '') }}"
                           placeholder="e.g., /usr/bin/pdflatex or C:\path\to\miktex\bin\x64\pdflatex.exe">
                    @error('pdflatex_path')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <small class="form-text text-muted">
Enter the full path to the `pdflatex` executable on your server. Ensure the web server has permission to execute it. Leave blank to attempt using the system's PATH.
</small>
                </div>

                <button type="submit" class="btn btn-primary">Save Configuration</button>
            </form>
        </div>
    </div>
@stop

@section('css')
    {{-- Add custom CSS here --}}
@stop

@section('js')
    {{-- Add custom JS here --}}
@stop 
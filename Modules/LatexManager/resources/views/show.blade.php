@extends('adminlte::page')

@section('title', 'View Latex Item')

@section('content_header')
    <h1>View Latex Item: {{ $latexItem->title }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">View Latex Item: {{ $latexItem->title }}</h3>
            <div class="card-tools">
                <a href="{{ route('admin.latex-manager.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
                <a href="{{ route('admin.latex-manager.edit', $latexItem->id) }}" class="btn btn-warning btn-sm">Edit Item</a>
            </div>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">ID</dt>
                <dd class="col-sm-9">{{ $latexItem->id }}</dd>

                <dt class="col-sm-3">Title</dt>
                <dd class="col-sm-9">{{ $latexItem->title }}</dd>

                <dt class="col-sm-3">Content</dt>
                <dd class="col-sm-9">{{ $latexItem->content ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Latex Editor Content</dt>
                {{-- Displaying raw LaTeX content. Consider rendering it if needed. --}}
                <dd class="col-sm-9"><pre><code>{{ $latexItem->latex_editor ?? 'N/A' }}</code></pre></dd>

                <dt class="col-sm-3">Created At</dt>
                <dd class="col-sm-9">{{ $latexItem->created_at }}</dd>

                <dt class="col-sm-3">Updated At</dt>
                <dd class="col-sm-9">{{ $latexItem->updated_at }}</dd>
            </dl>
        </div>
    </div>
@stop

@section('css')
    {{-- Add custom CSS here --}}
@stop

@section('js')
    {{-- Add custom JS here --}}
@stop

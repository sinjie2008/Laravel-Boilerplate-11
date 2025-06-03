@extends('adminlte::page')

@section('title', 'Service Details')

@section('content_header')
    <h1>Service Details</h1>
@stop

@section('content')
    <h1>Show Service</h1>

    <div class="form-group">
        <strong>ID:</strong>
        {{ $service['id'] }}
    </div>
    <div class="form-group">
        <strong>Name:</strong>
        {{ $service['name'] }}
    </div>
    <div class="form-group">
        <strong>Description:</strong>
        {{ $service['description'] }}
    </div>

    <a class="btn btn-primary" href="{{ route('serviceapi.services.edit', $service['id']) }}">Edit Service</a>
    <a class="btn btn-secondary" href="{{ route('serviceapi.services.index') }}">Back to Service List</a>
@endsection

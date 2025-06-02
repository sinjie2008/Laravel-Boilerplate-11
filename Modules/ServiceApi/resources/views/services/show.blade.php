@extends('adminlte::page')

@section('title', 'Service Details')

@section('content_header')
    <h1>Service Details</h1>
@stop

@section('content')

    <div>
        <strong>ID:</strong> {{ $service['id'] }}
    </div>
    <div>
        <strong>Name:</strong> {{ $service['name'] }}
    </div>
    <div>
        <strong>Description:</strong> {{ $service['description'] }}
    </div>

    <a href="{{ route('serviceapi.services.edit', $service['id']) }}">Edit Service</a>
    <a href="{{ route('serviceapi.services.index') }}">Back to Service List</a>
@endsection

@extends('adminlte::page')

@section('title', 'Edit Service')

@section('content_header')
    <h1>Edit Service</h1>
@stop

@section('content')
    <h1>Edit Service</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('serviceapi.services.update', $service['id']) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Service Name:</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $service['name']) }}" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description">{{ old('description', $service['description']) }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update Service</button>
        <a href="{{ route('serviceapi.services.index') }}" class="btn btn-secondary">Back to Service List</a>
    </form>
@endsection

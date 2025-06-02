@extends('adminlte::page')

@section('title', 'Edit Service')

@section('content_header')
    <h1>Edit Service</h1>
@stop

@section('content')

    @if ($errors->any())
        <div style="color: red;">
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
        <div>
            <label for="name">Service Name:</label>
            <input type="text" id="name" name="name" value="{{ old('name', $service['name']) }}" required>
        </div>
        <div>
            <label for="description">Description:</label>
            <textarea id="description" name="description">{{ old('description', $service['description']) }}</textarea>
        </div>
        <button type="submit">Update Service</button>
    </form>

    <a href="{{ route('serviceapi.services.index') }}">Back to Service List</a>
@endsection

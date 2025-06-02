@extends('adminlte::page')

@section('title', 'Create New Service')

@section('content_header')
    <h1>Create New Service</h1>
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

    <form action="{{ route('serviceapi.services.store') }}" method="POST">
        @csrf
        <div>
            <label for="name">Service Name:</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required>
        </div>
        <div>
            <label for="description">Description:</label>
            <textarea id="description" name="description">{{ old('description') }}</textarea>
        </div>
        <button type="submit">Create Service</button>
    </form>

    <a href="{{ route('serviceapi.services.index') }}">Back to Service List</a>
@endsection

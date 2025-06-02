@extends('adminlte::page')

@section('title', 'Service List')

@section('content_header')
    <h1>Service List</h1>
@stop

@section('content')

    @if (session('success'))
        <div style="color: green;">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('serviceapi.services.create') }}">Create New Service</a>

    @if (empty($services))
        <p>No services found.</p>
    @else
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($services as $service)
                    <tr>
                        <td>{{ $service['id'] }}</td>
                        <td>{{ $service['name'] }}</td>
                        <td>{{ $service['description'] }}</td>
                        <td>
                            <a href="{{ route('serviceapi.services.show', $service['id']) }}">View</a>
                            <a href="{{ route('serviceapi.services.edit', $service['id']) }}">Edit</a>
                            <form action="{{ route('serviceapi.services.destroy', $service['id']) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection

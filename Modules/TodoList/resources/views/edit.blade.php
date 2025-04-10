@extends('adminlte::page')

@section('content')
    <h1>Edit Todo</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.todolist.update', $todo->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ $todo->title }}" required>
        </div>

        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description">{{ $todo->description }}</textarea>
        </div>

        <div class="form-group">
            <label for="completed">Completed:</label>
            <input type="checkbox" id="completed" name="completed" {{ $todo->completed ? 'checked' : '' }}>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    <a class="btn btn-primary" href="{{ route('admin.todolist.index') }}">Back</a>
@endsection

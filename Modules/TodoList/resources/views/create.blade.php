@extends('adminlte::page')

@section('content')
    <h1>Create New Todo</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.todolist.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>

        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description"></textarea>
        </div>

        <div class="form-group">
            <label for="completed">Completed:</label>
            <input type="checkbox" id="completed" name="completed">
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
@endsection

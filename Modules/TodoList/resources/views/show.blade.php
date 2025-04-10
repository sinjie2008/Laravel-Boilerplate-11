@extends('adminlte::page')

@section('content')
    <h1>Show Todo</h1>

    <div class="form-group">
        <strong>Title:</strong>
        {{ $todo->title }}
    </div>

    <div class="form-group">
        <strong>Description:</strong>
        {{ $todo->description }}
    </div>

    <div class="form-group">
        <strong>Completed:</strong>
        {{ $todo->completed ? 'Yes' : 'No' }}
    </div>

    <a class="btn btn-primary" href="{{ route('admin.todolist.index') }}">Back</a>
@endsection

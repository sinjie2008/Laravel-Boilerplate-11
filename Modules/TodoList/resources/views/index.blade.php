@extends('adminlte::page')

@section('content')
    <h1>Todo List</h1>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <a href="{{ route('admin.todolist.create') }}" class="btn btn-success">Create New Todo</a>

    <table class="table table-bordered">
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Completed</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($todos as $todo)
        <tr>
            <td>{{ $todo->title }}</td>
            <td>{{ $todo->description }}</td>
            <td>{{ $todo->completed ? 'Yes' : 'No' }}</td>
            <td>
                <form action="{{ route('admin.todolist.destroy',$todo->id) }}" method="POST">

                    <a class="btn btn-info" href="{{ route('admin.todolist.show',$todo->id) }}">Show</a>

                    <a class="btn btn-primary" href="{{ route('admin.todolist.edit',$todo->id) }}">Edit</a>

                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
@endsection

@extends('adminlte::page')
@section('title', 'Approval Items')
@section('content')
<div class="mb-3">
    <a href="{{ route('approval.items.create') }}" class="btn btn-primary">New Item</a>
</div>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Title</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    @foreach($items as $item)
        <tr>
            <td>{{ $item->title }}</td>
            <td>
                <a href="{{ route('approval.items.show', $item) }}" class="btn btn-info btn-sm">View</a>
                <a href="{{ route('approval.items.edit', $item) }}" class="btn btn-secondary btn-sm">Edit</a>
                <form action="{{ route('approval.items.destroy', $item) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">Delete</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection

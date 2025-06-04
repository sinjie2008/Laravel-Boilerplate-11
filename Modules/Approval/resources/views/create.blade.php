@extends('adminlte::page')
@section('title', 'Create Approval Item')
@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('approval.items.store') }}">
            @csrf
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" name="title" id="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</div>
@endsection

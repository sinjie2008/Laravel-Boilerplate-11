@extends('adminlte::page')

@section('content')
    <h1>Create Fund Request</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.fund-request.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="amount">Amount:</label>
            <input type="number" class="form-control" id="amount" name="amount" required step="0.01">
        </div>

        <div class="form-group">
            <label for="purpose">Purpose:</label>
            <textarea class="form-control" id="purpose" name="purpose" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
@endsection

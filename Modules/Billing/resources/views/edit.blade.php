@extends('adminlte::page')

@section('content')
    <h1>Edit Invoice</h1>
    <form action="{{ route('admin.billing.update', $invoice) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>User</label>
            <select name="user_id" class="form-control">
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $invoice->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Amount</label>
            <input type="number" step="0.01" name="amount" class="form-control" value="{{ $invoice->amount }}" required>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="pending" {{ $invoice->status=='pending' ? 'selected' : '' }}>Pending</option>
                <option value="paid" {{ $invoice->status=='paid' ? 'selected' : '' }}>Paid</option>
                <option value="refunded" {{ $invoice->status=='refunded' ? 'selected' : '' }}>Refunded</option>
            </select>
        </div>
        <div class="form-group">
            <label>Due Date</label>
            <input type="date" name="due_date" class="form-control" value="{{ $invoice->due_date->format('Y-m-d') }}" required>
        </div>
        <button class="btn btn-primary">Save</button>
    </form>
@endsection

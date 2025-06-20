@extends('adminlte::page')

@section('content')
    <h1>Edit Plan</h1>
    <form action="{{ route('admin.plans.update', $plan) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ $plan->name }}" required>
        </div>
        <div class="form-group">
            <label>Amount</label>
            <input type="number" step="0.01" name="amount" class="form-control" value="{{ $plan->amount }}" required>
        </div>
        <div class="form-group">
            <label>Billing Interval</label>
            <select name="billing_interval" class="form-control">
                <option value="month" {{ $plan->billing_interval=='month' ? 'selected' : '' }}>Monthly</option>
                <option value="year" {{ $plan->billing_interval=='year' ? 'selected' : '' }}>Yearly</option>
            </select>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="active" {{ $plan->status=='active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ $plan->status=='inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <button class="btn btn-primary">Save</button>
    </form>
@endsection

@extends('adminlte::page')

@section('title', 'Create Plan')

@section('content')
    <h1>Create Plan</h1>

    <form method="POST" action="{{ route('admin.billing.plans.store') }}">
        @csrf
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="slug">Slug</label>
            <input type="text" id="slug" name="slug" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="price">Price (cents)</label>
            <input type="number" id="price" name="price" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="currency">Currency</label>
            <input type="text" id="currency" name="currency" class="form-control" value="usd" required>
        </div>
        <div class="form-group">
            <label for="stripe_price_id">Stripe Price ID</label>
            <input type="text" id="stripe_price_id" name="stripe_price_id" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
@endsection

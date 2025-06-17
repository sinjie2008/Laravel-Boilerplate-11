@extends('adminlte::page')

@section('title', 'Plans')

@section('content')
    <h1>Plans</h1>

    <a href="{{ route('admin.billing.plans.create') }}" class="btn btn-primary mb-3">New Plan</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($plans as $plan)
                <tr>
                    <td>{{ $plan->name }}</td>
                    <td>{{ number_format($plan->price / 100, 2) }} {{ strtoupper($plan->currency) }}</td>
                    <td>
                        <a href="{{ route('admin.billing.plans.edit', $plan) }}" class="btn btn-sm btn-secondary">Edit</a>
                        <form action="{{ route('admin.billing.plans.destroy', $plan) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

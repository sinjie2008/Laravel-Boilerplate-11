@extends('adminlte::page')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <h1>Plans</h1>
        @can('manage-billing')
            <a href="{{ route('admin.plans.create') }}" class="btn btn-primary">New Plan</a>
        @endcan
    </div>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Amount</th>
            <th>Interval</th>
            <th>Status</th>
            <th>Synced</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($plans as $plan)
            <tr>
                <td>{{ $plan->id }}</td>
                <td>{{ $plan->name }}</td>
                <td>{{ $plan->amount }}</td>
                <td>{{ $plan->billing_interval }}</td>
                <td>{{ $plan->status }}</td>
                <td>{!! $plan->synced ? '<span class="text-success">✓</span>' : '<span class="text-danger">✗</span>' !!}</td>
                <td>
                    @can('manage-billing')
                        <a href="{{ route('admin.plans.edit', $plan) }}" class="btn btn-sm btn-primary">Edit</a>
                        <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    @endcan
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

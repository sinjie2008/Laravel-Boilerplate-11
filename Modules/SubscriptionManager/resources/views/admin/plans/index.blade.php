@extends('adminlte::page')

@section('title', 'Manage Plans')

@section('content_header')
    <h1>Manage Plans</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Plan List</h3>
            <div class="card-tools">
                <a href="{{ route('admin.plans.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New Plan
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>API Call Limit/Day</th>
                        <th style="width: 150px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($plans as $plan)
                        <tr>
                            <td>{{ $plan->id }}</td>
                            <td>{{ $plan->name }}</td>
                            <td>{{ number_format($plan->price, 2) }}</td>
                            <td>{{ $plan->api_call_limit_per_day }}</td>
                            <td>
                                <a href="{{ route('admin.plans.show', $plan) }}" class="btn btn-info btn-xs">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.plans.edit', $plan) }}" class="btn btn-warning btn-xs">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure you want to delete this plan?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No plans found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($plans->hasPages())
            <div class="card-footer clearfix">
                {{ $plans->links() }}
            </div>
        @endif
    </div>
@stop
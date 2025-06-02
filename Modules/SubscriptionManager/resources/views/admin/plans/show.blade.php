@extends('adminlte::page')

@section('title', 'View Plan')

@section('content_header')
    <h1>View Plan: {{ $plan->name }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Plan Details</h3>
            <div class="card-tools">
                <a href="{{ route('admin.plans.edit', $plan) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('admin.plans.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th style="width: 200px;">ID</th>
                        <td>{{ $plan->id }}</td>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <td>{{ $plan->name }}</td>
                    </tr>
                    <tr>
                        <th>Price</th>
                        <td>{{ number_format($plan->price, 2) }}</td>
                    </tr>
                    <tr>
                        <th>API Call Limit Per Day</th>
                        <td>{{ $plan->api_call_limit_per_day }}</td>
                    </tr>
                    <tr>
                        <th>Permissions</th>
                        <td>
                            @forelse ($plan->permissions as $permission)
                                <span class="badge badge-primary">{{ $permission->name }}</span>
                            @empty
                                <span class="badge badge-secondary">No permissions assigned</span>
                            @endforelse
                        </td>
                    </tr>
                    <tr>
                        <th>Created At</th>
                        <td>{{ $plan->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>Updated At</th>
                        <td>{{ $plan->updated_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@stop
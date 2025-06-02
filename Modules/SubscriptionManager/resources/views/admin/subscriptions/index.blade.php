@extends('adminlte::page')

@section('title', 'Manage User Subscriptions')

@section('content_header')
    <h1>Manage User Subscriptions</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Subscription List</h3>
            <div class="card-tools">
                <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New Subscription
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>User</th>
                        <th>Plan</th>
                        <th>Status</th>
                        <th>Starts At</th>
                        <th>Ends At</th>
                        <th style="width: 150px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($subscriptions as $subscription)
                        <tr>
                            <td>{{ $subscription->id }}</td>
                            <td>{{ $subscription->user->name ?? 'N/A' }} ({{ $subscription->user->email ?? 'N/A' }})</td>
                            <td>{{ $subscription->plan->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge badge-{{ $subscription->status === 'active' ? 'success' : ($subscription->status === 'pending' ? 'warning' : ($subscription->status === 'expired' ? 'secondary' : 'danger')) }}">
                                    {{ ucfirst($subscription->status) }}
                                </span>
                            </td>
                            <td>{{ $subscription->starts_at ? $subscription->starts_at->format('Y-m-d H:i') : 'N/A' }}</td>
                            <td>{{ $subscription->ends_at ? $subscription->ends_at->format('Y-m-d H:i') : 'N/A' }}</td>
                            <td>
                                <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="btn btn-info btn-xs">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="btn btn-warning btn-xs">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.subscriptions.destroy', $subscription) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure you want to delete this subscription? This will also revoke associated API tokens and permissions.')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No subscriptions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($subscriptions->hasPages())
            <div class="card-footer clearfix">
                {{ $subscriptions->links() }}
            </div>
        @endif
    </div>
@stop
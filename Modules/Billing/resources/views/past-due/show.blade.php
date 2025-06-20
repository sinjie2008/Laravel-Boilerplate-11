@extends('adminlte::page')

@section('content')
    <h1>Retry History for Subscription #{{ $subscription->id }}</h1>

    <ul class="list-group mb-3">
        <li class="list-group-item">User: {{ $subscription->user->name }} ({{ $subscription->user->email }})</li>
        <li class="list-group-item">Plan: {{ $subscription->plan->name ?? $subscription->stripe_price }}</li>
        <li class="list-group-item">Status: {{ $subscription->stripe_status }}</li>
    </ul>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Status</th>
                <th>Failure Reason</th>
            </tr>
        </thead>
        <tbody>
        @foreach($subscription->paymentRetries as $retry)
            <tr>
                <td>{{ $retry->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ $retry->status }}</td>
                <td>{{ $retry->failure_reason ?? 'N/A' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <a href="{{ route('admin.past-due.index') }}" class="btn btn-secondary">Back</a>
@endsection

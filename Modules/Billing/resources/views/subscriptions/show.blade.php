@extends('adminlte::page')

@section('content')
    <h1>Subscription #{{ $subscription->id }}</h1>
    <ul class="list-group mb-3">
        <li class="list-group-item">User: {{ $subscription->user->name }} ({{ $subscription->user->email }})</li>
        <li class="list-group-item">Plan: {{ $subscription->plan->name ?? $subscription->stripe_price }}</li>
        <li class="list-group-item">Status: {{ $subscription->stripe_status }}</li>
        <li class="list-group-item">Trial Ends At: {{ optional($subscription->trial_ends_at)->format('Y-m-d') }}</li>
        <li class="list-group-item">Current Period End: {{ optional($subscription->ends_at)->format('Y-m-d') }}</li>
    </ul>
    <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary">Back</a>
@endsection

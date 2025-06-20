@extends('adminlte::page')

@section('content')
    <h1>Past-due Subscriptions</h1>

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>User</th>
                <th>Plan</th>
                <th>Latest Failure</th>
                <th>Retries</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        @foreach($subscriptions as $subscription)
            <tr>
                <td>{{ $subscription->user->name }} ({{ $subscription->user->email }})</td>
                <td>{{ $subscription->plan->name ?? $subscription->stripe_price }}</td>
                <td>{{ $subscription->paymentRetries->first()->failure_reason ?? 'N/A' }}</td>
                <td>{{ $subscription->paymentRetries->count() }}</td>
                <td class="d-flex gap-2">
                    <a href="{{ route('admin.past-due.show', $subscription) }}" class="btn btn-sm btn-secondary">History</a>
                    <form action="{{ route('admin.past-due.retry', $subscription) }}" method="POST">
                        @csrf
                        <button class="btn btn-sm btn-warning" type="submit">Retry</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $subscriptions->links() }}
@endsection

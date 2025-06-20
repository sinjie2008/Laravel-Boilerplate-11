@extends('adminlte::page')

@section('content')
    <h1>Subscriptions</h1>

    <form method="GET" class="mb-3 d-flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search user" class="form-control w-auto">
        <select name="plan" class="form-control w-auto">
            <option value="">All Plans</option>
            @foreach($plans as $id => $name)
                <option value="{{ $id }}" @selected(request('plan') == $id)>{{ $name }}</option>
            @endforeach
        </select>
        <select name="status" class="form-control w-auto">
            <option value="">All Statuses</option>
            @foreach($statuses as $status)
                <option value="{{ $status }}" @selected(request('status') == $status)>{{ $status }}</option>
            @endforeach
        </select>
        <button class="btn btn-primary" type="submit">Filter</button>
    </form>

    <table class="table table-bordered table-hover" id="subscriptions-table">
        <thead>
            <tr>
                <th>User</th>
                <th>Email</th>
                <th>Plan</th>
                <th>Status</th>
                <th>Trial End</th>
                <th>Current Period End</th>
            </tr>
        </thead>
        <tbody>
        @foreach($subscriptions as $subscription)
            <tr data-href="{{ route('admin.subscriptions.show', $subscription) }}" style="cursor:pointer">
                <td>{{ $subscription->user->name }}</td>
                <td>{{ $subscription->user->email }}</td>
                <td>{{ $subscription->plan->name ?? $subscription->stripe_price }}</td>
                <td>{{ $subscription->stripe_status }}</td>
                <td>{{ optional($subscription->trial_ends_at)->format('Y-m-d') }}</td>
                <td>{{ optional($subscription->ends_at)->format('Y-m-d') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $subscriptions->links() }}
@endsection

@section('js')
    <script>
        document.querySelectorAll('#subscriptions-table tbody tr').forEach(row => {
            row.addEventListener('click', () => {
                window.location = row.dataset.href;
            });
        });
    </script>
@endsection

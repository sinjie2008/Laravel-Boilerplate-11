@extends('adminlte::page')

@section('content')
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h3>${{ number_format($mrr, 2) }}</h3>
                    <p class="text-muted mb-0">Monthly Recurring Revenue</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h3>{{ number_format($churnRate, 2) }}%</h3>
                    <p class="text-muted mb-0">Churn Rate</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h3>{{ $pastDueCount }}</h3>
                    <p class="text-muted mb-0">Past-due Subscriptions</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Revenue Last 30 Days</div>
        <div class="card-body">
            <canvas id="revenue-chart" height="80"></canvas>
        </div>
    </div>

    <div class="d-flex justify-content-between mb-3">
        <h1>Invoices</h1>
        @can('manage-billing')
            <a href="{{ route('admin.billing.create') }}" class="btn btn-primary">New Invoice</a>
        @endcan
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Due Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        @foreach($invoices as $invoice)
            <tr>
                <td>{{ $invoice->id }}</td>
                <td>
                    {{ $invoice->user->name }}
                    @can('manage-billing')
                        <form action="{{ route('admin.impersonate.start', $invoice->user) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-xs btn-secondary" type="submit">Impersonate</button>
                        </form>
                    @endcan
                </td>
                <td>{{ $invoice->amount }}</td>
                <td>{{ $invoice->status }}</td>
                <td>{{ $invoice->due_date->format('Y-m-d') }}</td>
                <td>
                    @can('manage-billing')
                        <a href="{{ route('admin.billing.edit', $invoice) }}" class="btn btn-sm btn-primary">Edit</a>
                        <form action="{{ route('admin.billing.destroy', $invoice) }}" method="POST" style="display:inline-block">
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

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
        const ctx = document.getElementById('revenue-chart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($revenueData->keys()),
                datasets: [{
                    label: 'Revenue',
                    data: @json($revenueData->values()),
                    tension: 0.4,
                    borderColor: '#3c8dbc',
                    backgroundColor: 'rgba(60,141,188,0.2)',
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    </script>
@endsection

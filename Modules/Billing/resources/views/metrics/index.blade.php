@extends('adminlte::page')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <h1>Billing Metrics</h1>
        <div>
            <a href="{{ route('admin.metrics.export', 'revenue') }}" class="btn btn-sm btn-primary">Export Revenue CSV</a>
            <a href="{{ route('admin.metrics.export', 'churn') }}" class="btn btn-sm btn-primary">Export Churn CSV</a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Monthly Revenue ({{ now()->year }})</div>
        <div class="card-body">
            <canvas id="revenue-chart" height="80"></canvas>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Churn Rate ({{ now()->year }})</div>
        <div class="card-body">
            <canvas id="churn-chart" height="80"></canvas>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
        new Chart(document.getElementById('revenue-chart'), {
            type: 'line',
            data: {
                labels: @json(array_values(array_map(fn($m) => sprintf('%02d', $m), array_keys($revenue)))),
                datasets: [{
                    label: 'Revenue',
                    data: @json(array_values($revenue)),
                    tension: 0.4,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40,167,69,0.2)',
                }]
            },
            options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
        });

        new Chart(document.getElementById('churn-chart'), {
            type: 'line',
            data: {
                labels: @json(array_values(array_map(fn($m) => sprintf('%02d', $m), array_keys($churn)))),
                datasets: [{
                    label: 'Churn %',
                    data: @json(array_values($churn)),
                    tension: 0.4,
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220,53,69,0.2)',
                }]
            },
            options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
        });
    </script>
@endsection

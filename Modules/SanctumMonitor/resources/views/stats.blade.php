@extends('adminlte::page')

@section('content_header')
    <h1>API Statistics</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">API Statistics Overview</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-chart-bar"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Requests</span>
                            <span class="info-box-number">{{ $totalRequests }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <h3 class="mt-4">Top Routes</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Route</th>
                        <th>Total Requests</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topRoutes as $route)
                        <tr>
                            <td>{{ $route->route }}</td>
                            <td>{{ $route->total }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <h3 class="mt-4">Most Active Users</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Total Requests</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topUsers as $user)
                        <tr>
                            <td>{{ optional($user->user)->name ?? 'N/A' }}</td>
                            <td>{{ $user->total }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

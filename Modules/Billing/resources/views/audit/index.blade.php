@extends('adminlte::page')

@section('content')
    <h1>Billing Audit Log</h1>

    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>Action</th>
            <th>User</th>
            <th>Model</th>
            <th>Timestamp</th>
        </tr>
        </thead>
        <tbody>
        @foreach($activities as $activity)
            <tr>
                <td>{{ $activity->description }}</td>
                <td>{{ optional($activity->causer)->name ?? 'System' }}</td>
                <td>{{ class_basename($activity->subject_type) }} #{{ $activity->subject_id }}</td>
                <td>{{ $activity->created_at->format('Y-m-d H:i:s') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $activities->links() }}
@endsection

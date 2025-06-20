@extends('adminlte::page')

@section('content')
    <h1>Webhook Logs</h1>

    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>Event Type</th>
            <th>Created At</th>
            <th>Replayed At</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($webhooks as $webhook)
            <tr>
                <td>{{ $webhook->event_type }}</td>
                <td>{{ $webhook->created_at->format('Y-m-d H:i:s') }}</td>
                <td>{{ $webhook->replayed_at?->format('Y-m-d H:i:s') ?? 'N/A' }}</td>
                <td>
                    <form action="{{ route('admin.webhooks.replay', $webhook) }}" method="POST">
                        @csrf
                        <button class="btn btn-sm btn-primary" type="submit">Replay</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $webhooks->links() }}
@endsection

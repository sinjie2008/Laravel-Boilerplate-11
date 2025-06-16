@extends('sanctummonitor::layouts.master')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-xl font-bold mb-4">Tokens</h2>
    <table class="table-auto w-full">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Name</th>
                <th>IP</th>
                <th>Abilities</th>
                <th>Created</th>
                <th>Last Used</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($tokens as $token)
            <tr>
                <td>{{ $token->id }}</td>
                <td>{{ optional($token->tokenable)->name }}</td>
                <td>{{ $token->name }}</td>
                <td>{{ $token->ip }}</td>
                <td>{{ implode(',', $token->abilities ?? []) }}</td>
                <td>{{ $token->created_at }}</td>
                <td>{{ $token->last_used_at }}</td>
                <td>
                    <form action="{{ route('admin.sanctummonitor.tokens.revoke', $token->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Revoke</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

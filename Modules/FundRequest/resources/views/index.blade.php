@extends('fundrequest::layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Fund Requests</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.fundrequests.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Create New Fund Request
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif

                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Requester</th>
                                    <th>Amount</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($fundRequests as $request)
                                    <tr>
                                        <td>{{ $request->id }}</td>
                                        <td>{{ $request->user->name ?? 'N/A' }}</td>
                                        <td>{{ number_format($request->amount, 2) }}</td>
                                        <td>{{ Str::limit($request->description, 50) }}</td>
                                        <td>
                                            <span class="badge
                                                @if($request->status === 'approved') badge-success
                                                @elseif($request->status === 'rejected') badge-danger
                                                @elseif(Str::startsWith($request->status, 'pending_')) badge-warning
                                                @else badge-secondary
                                                @endif">
                                                {{ Str::title(str_replace('_', ' ', $request->status)) }}
                                            </span>
                                        </td>
                                        <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.fundrequests.show', $request->id) }}" class="btn btn-info btn-xs" title="View"><i class="fas fa-eye"></i></a>
                                            @if (Auth::id() === $request->user_id && $request->status === 'pending_level1_approval')
                                                <a href="{{ route('admin.fundrequests.edit', $request->id) }}" class="btn btn-warning btn-xs" title="Edit"><i class="fas fa-edit"></i></a>
                                                <form action="{{ route('admin.fundrequests.destroy', $request->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this request?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-xs" title="Delete"><i class="fas fa-trash"></i></button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No fund requests found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3">
                            {{ $fundRequests->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

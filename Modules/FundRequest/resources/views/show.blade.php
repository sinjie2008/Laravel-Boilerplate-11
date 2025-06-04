@extends('fundrequest::layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Fund Request Details #{{ $fundRequest->id }}</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.fundrequests.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to List
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

                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th style="width: 200px;">ID</th>
                                    <td>{{ $fundRequest->id }}</td>
                                </tr>
                                <tr>
                                    <th>Requester</th>
                                    <td>{{ $fundRequest->user->name ?? 'N/A' }} ({{ $fundRequest->user->email ?? 'N/A' }})</td>
                                </tr>
                                <tr>
                                    <th>Amount</th>
                                    <td>{{ number_format($fundRequest->amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td>{{ $fundRequest->description }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge
                                            @if($fundRequest->status === 'approved') badge-success
                                            @elseif($fundRequest->status === 'rejected') badge-danger
                                            @elseif(Str::startsWith($fundRequest->status, 'pending_')) badge-warning
                                            @else badge-secondary
                                            @endif">
                                            {{ Str::title(str_replace('_', ' ', $fundRequest->status)) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $fundRequest->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated At</th>
                                    <td>{{ $fundRequest->updated_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <hr>
                        <h4>Approval History</h4>
                        @if($fundRequest->approvals()->count() > 0)
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Step</th>
                                        <th>Action By</th>
                                        <th>Action</th>
                                        <th>Comments</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($fundRequest->approvals()->orderBy('created_at', 'desc')->get() as $action)
                                    <tr>
                                        <td>{{ Str::title(str_replace('_', ' ', $action->step_name)) }}</td>
                                        <td>{{ $action->user->name ?? 'System' }}</td>
                                        <td>
                                            <span class="badge {{ $action->action === 'APPROVED' ? 'badge-success' : ($action->action === 'REJECTED' ? 'badge-danger' : 'badge-info') }}">
                                                {{ Str::title($action->action) }}
                                            </span>
                                        </td>
                                        <td>{{ $action->comments }}</td>
                                        <td>{{ $action->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p>No approval actions have been recorded yet.</p>
                        @endif

                        @if ($fundRequest->status !== 'approved' && $fundRequest->status !== 'rejected' && $fundRequest->canBeApprovedBy(Auth::user()))
                            <hr>
                            <h4>Take Action</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <form action="{{ route('admin.fundrequests.approve', $fundRequest->id) }}" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <label for="approve_comments">Approval Comments (Optional)</label>
                                            <textarea name="comments" id="approve_comments" class="form-control" rows="3"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Approve</button>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <form action="{{ route('admin.fundrequests.reject', $fundRequest->id) }}" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <label for="reject_comments">Rejection Comments (Required)</label>
                                            <textarea name="comments" id="reject_comments" class="form-control @error('comments') is-invalid @enderror" rows="3" required></textarea>
                                            @error('comments')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <button type="submit" class="btn btn-danger"><i class="fas fa-times"></i> Reject</button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
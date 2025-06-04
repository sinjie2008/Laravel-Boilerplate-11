@extends('adminlte::page')

@section('title', 'Fund Requests')

@section('content_header')
<div class="row">
    <div class="col-sm-6">
        <h1>Fund Requests</h1>
    </div>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="card">
            <div class="card-header">
                @can('create fund requests')
                <a href="{{ route('fundrequest.fundrequests.create') }}" class="btn btn-primary">New Fund Request</a>
                @endcan
            </div>
            <div class="card-body">
                <table id="fundrequest-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fundRequests as $fundRequest)
                        <tr>
                            <td>{{ $fundRequest->amount }}</td>
                            <td>{{ $fundRequest->description }}</td>
                            <td>
                                @can('view fund requests')
                                <a href="{{ route('fundrequest.fundrequests.show', $fundRequest) }}" class="btn btn-info btn-sm">View</a>
                                @endcan
                                @can('update fund requests')
                                <a href="{{ route('fundrequest.fundrequests.edit', $fundRequest) }}" class="btn btn-primary btn-sm">Edit</a>
                                @endcan
                                @can('delete fund requests')
                                <form action="{{ route('fundrequest.fundrequests.destroy', $fundRequest) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
@stop

@section('js')
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
    $('#fundrequest-table').DataTable({
        responsive: true,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search fund requests...",
        }
    });
});
</script>
@stop

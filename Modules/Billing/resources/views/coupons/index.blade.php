@extends('adminlte::page')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <h1>Coupons</h1>
        @can('manage-billing')
            <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">New Coupon</a>
        @endcan
    </div>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Code</th>
            <th>Amount Off</th>
            <th>Percent Off</th>
            <th>Duration</th>
            <th>Synced</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($coupons as $coupon)
            <tr>
                <td>{{ $coupon->id }}</td>
                <td>{{ $coupon->code }}</td>
                <td>{{ $coupon->amount_off }}</td>
                <td>{{ $coupon->percent_off }}</td>
                <td>{{ $coupon->duration }}</td>
                <td>{!! $coupon->synced ? '<span class="text-success">✓</span>' : '<span class="text-danger">✗</span>' !!}</td>
                <td>
                    @can('manage-billing')
                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-primary">Edit</a>
                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" style="display:inline-block">
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

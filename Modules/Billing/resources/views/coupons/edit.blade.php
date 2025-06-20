@extends('adminlte::page')

@section('content')
    <h1>Edit Coupon</h1>
    <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
        @csrf
        @method('PUT')
        @include('billing::coupons.form')
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
@endsection

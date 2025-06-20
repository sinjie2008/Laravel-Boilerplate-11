@extends('adminlte::page')

@section('content')
    <h1>New Coupon</h1>
    <form action="{{ route('admin.coupons.store') }}" method="POST">
        @csrf
        @include('billing::coupons.form')
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
@endsection

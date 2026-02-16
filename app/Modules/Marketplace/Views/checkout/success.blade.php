@extends('layouts.layoutMaster')

@section('title', 'Order Success')

@section('content')
    <x-feature-section feature="marketplace.view-order-details">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card text-center">
                        <div class="card-body p-5">
                            <div class="mb-4">
                                <i class="bx bx-check-circle text-success" style="font-size: 5rem;"></i>
                            </div>
                            <h2 class="mb-3">Thank you for your order!</h2>
                            <p class="lead mb-4">Your order <strong>#{{ $order->reference ?? $order->id }}</strong> has been
                                placed successfully.</p>

                            <div class="d-flex justify-content-center gap-3">
                                <a href="{{ route('marketplace.index') }}" class="btn btn-outline-primary">Continue
                                    Shopping</a>
                                <a href="{{ route('user.orders.show', $order->id) }}" class="btn btn-primary">View Order</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-feature-section>
@endsection

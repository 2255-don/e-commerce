@extends('layouts.layoutMaster')

@section('title', 'Checkout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4">
            <span class="text-muted fw-light">Marketplace /</span> {{ __('Checkout') }}
        </h4>

        <div class="row">
            <!-- Checkout Details -->
            <x-feature-section feature="marketplace.checkout-form" showDeniedMessage="true">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title m-0">{{ __('Shipping & Payment') }}</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
                                @csrf

                                <!-- Address (Simplified for now) -->
                                <div class="mb-4">
                                    <label class="form-label" for="address">{{ __('Delivery Address') }}</label>
                                    <textarea class="form-control" id="address" name="delivery_address" rows="3"
                                        placeholder="{{ __('Enter your delivery address...') }}"></textarea>
                                </div>

                                <!-- Notes -->
                                <div class="mb-4">
                                    <label class="form-label" for="notes">{{ __('Order Notes (Optional)') }}</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="2"
                                        placeholder="{{ __('Any special instructions?') }}"></textarea>
                                </div>

                                <hr class="my-4">

                                <!-- Payment Method -->
                                <h5 class="mb-3">{{ __('Payment Method') }}</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-check custom-option custom-option-basic">
                                            <label class="form-check-label custom-option-content" for="paymentWallet">
                                                <input name="payment_method" class="form-check-input" type="radio"
                                                    value="wallet" id="paymentWallet" checked />
                                                <span class="custom-option-header">
                                                    <span class="h6 mb-0">{{ __('Wallet Balance') }}</span>
                                                    <small
                                                        class="text-muted">{{ __('Pay securely with your wallet') }}</small>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check custom-option custom-option-basic">
                                            <label class="form-check-label custom-option-content" for="paymentMobile">
                                                <input name="payment_method" class="form-check-input" type="radio"
                                                    value="mobile_money" id="paymentMobile" />
                                                <span class="custom-option-header">
                                                    <span class="h6 mb-0">{{ __('Mobile Money') }}</span>
                                                    <small class="text-muted">Orange / MTN / Moov</small>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <x-feature-button feature="marketplace.place-order" type="submit"
                                        class="btn btn-primary w-100">
                                        {{ __('Place Order') }}
                                    </x-feature-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </x-feature-section>

            <!-- Order Summary -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title m-0">{{ __('Order Summary') }}</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush mb-3">
                            @foreach ($cart->items as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>{{ $item->product->title }} (x{{ $item->quantity }})</span>
                                    <span>{{ number_format($item->subtotal, 0, ',', ' ') }} FCFA</span>
                                </li>
                            @endforeach

                            <li
                                class="list-group-item d-flex justify-content-between align-items-center px-0 border-top mt-3 pt-3">
                                <strong>{{ __('Total') }}</strong>
                                <strong>{{ number_format($cart->total, 0, ',', ' ') }} FCFA</strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

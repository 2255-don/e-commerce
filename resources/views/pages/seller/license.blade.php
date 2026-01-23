@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Achat de Licence Vendeur')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Vendeur /</span> Licence
</h4>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card mb-4">
            <h5 class="card-header text-center">Devenir Vendeur Profite des avantages</h5>
            <div class="card-body">
                <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                    <span class="alert-icon text-info me-2">
                        <i class="ti ti-info-circle ti-xs"></i>
                    </span>
                    La licence vendeur coûte <strong>5 000 FCFA</strong> et est valable pour une durée de <strong>3 mois</strong>.
                </div>

                <div class="payment-selection mb-4">
                    <h6 class="mb-3">Choisissez votre mode de paiement :</h6>
                    
                    <!-- Wallet Option -->
                    <div class="card bg-lighter border mb-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="avatar bg-label-success me-3">
                                        <span class="avatar-initial rounded"><i class="ti ti-wallet"></i></span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Payer via Wallet Interner</h6>
                                        <small class="text-muted">Solde disponible : {{ number_format($wallet->balance ?? 0, 0, ',', ' ') }} FCFA</small>
                                    </div>
                                </div>
                                <form action="{{ route('seller.license.wallet') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm" {{ ($wallet->balance ?? 0) < 5000 ? 'disabled' : '' }}>
                                        Utiliser Wallet
                                    </button>
                                </form>
                            </div>
                            @if(($wallet->balance ?? 0) < 5000)
                            <div class="mt-2 text-danger small">
                                <i class="ti ti-alert-triangle ti-xs"></i> Solde insuffisant pour cet achat.
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Direct Payment Option (Mobile Money Simulator) -->
                    <div class="card bg-lighter border opacity-50">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="avatar bg-label-info me-3">
                                        <span class="avatar-initial rounded"><i class="ti ti-device-mobile"></i></span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Paiement Mobile Money Direct</h6>
                                        <small class="text-muted">Bientôt disponible</small>
                                    </div>
                                </div>
                                <button class="btn btn-outline-secondary btn-sm" disabled>Choisir</button>
                            </div>
                        </div>
                    </div>
                </div>

                @if(($wallet->balance ?? 0) < 5000)
                <div class="text-center mt-4">
                    <p class="mb-2 text-muted">Avez-vous besoin de recharger ?</p>
                    <a href="{{ route('wallet.recharge') }}" class="btn btn-success">
                        <i class="ti ti-plus me-1"></i> Recharger mon Wallet
                    </a>
                </div>
                @endif

                <div class="mt-4 text-center">
                    <a href="{{ route('profile.show') }}" class="text-muted small">Retour au profil</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts.layoutMaster')

@section('title', 'Achat de Licence Vendeur')

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Vendeur /</span> Licence
    </h4>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card mb-4">
                <h5 class="card-header text-center">
                    @if (Auth::user()->sellerProfile && Auth::user()->sellerProfile->status === 'pending')
                        <i class="ti ti-clock text-warning me-2"></i> Demande en cours de traitement
                    @elseif(Auth::user()->sellerProfile && Auth::user()->sellerProfile->status === 'rejected')
                        <i class="ti ti-alert-triangle text-danger me-2"></i> Demande rejetée
                    @else
                        Devenir Vendeur - Informations Requises
                    @endif
                </h5>

                <div class="card-body">
                    {{-- ALERTES D'INFORMATION --}}
                    @if (Auth::user()->sellerProfile && Auth::user()->sellerProfile->status === 'pending')
                        <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                            <span class="alert-icon text-warning me-2">
                                <i class="ti ti-clock ti-xs"></i>
                            </span>
                            Votre demande pour devenir vendeur est actuellement <strong>en attente d'approbation</strong>
                            par un administrateur. Vous recevrez une notification une fois traitée.
                        </div>
                        <div class="text-center">
                            <a href="{{ route('profile.show') }}" class="btn btn-primary">Retour au profil</a>
                        </div>
                    @else
                        {{-- FORMULAIRE D'INSCRIPTION --}}
                        @if (Auth::user()->sellerProfile && Auth::user()->sellerProfile->status === 'rejected')
                            <div class="alert alert-danger mb-4" role="alert">
                                <h6 class="alert-heading mb-1"><i class="ti ti-alert-circle me-1"></i> Votre demande
                                    précédente a été rejetée</h6>
                                <p class="mb-0">
                                    {{ Auth::user()->sellerProfile->rejection_reason ?? 'Motif non spécifié' }}</p>
                                <hr>
                                <p class="mb-0">Veuillez corriger les informations et soumettre une nouvelle demande.</p>
                            </div>
                        @else
                            <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                                <span class="alert-icon text-info me-2">
                                    <i class="ti ti-info-circle ti-xs"></i>
                                </span>
                                La licence vendeur coûte <strong>5 000 FCFA</strong> (déduit du wallet) et est valable
                                <strong>3 mois</strong>.
                            </div>
                        @endif

                        <form action="{{ route('seller.register') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            {{-- SHOP NAME --}}
                            <div class="mb-3">
                                <label for="shop_name" class="form-label">Nom de la Boutique <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('shop_name') is-invalid @enderror"
                                    id="shop_name" name="shop_name"
                                    value="{{ old('shop_name', Auth::user()->sellerProfile->shop_name ?? '') }}"
                                    placeholder="Ma Super Boutique" required>
                                @error('shop_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- LOGO --}}
                            <div class="mb-3">
                                <label for="logo" class="form-label">Logo de la Boutique (Optionnel)</label>
                                <input type="file" class="form-control @error('logo') is-invalid @enderror"
                                    id="logo" name="logo" accept="image/png, image/jpeg">
                                <div class="form-text">Format: PNG, JPG. Max 2MB.</div>
                                @error('logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- KYC DOCUMENT --}}
                            <div class="mb-4">
                                <label for="kyc_document" class="form-label">Document d'Identité (KYC) <span
                                        class="text-danger">*</span></label>
                                <input type="file" class="form-control @error('kyc_document') is-invalid @enderror"
                                    id="kyc_document" name="kyc_document" accept=".pdf, .jpg, .jpeg, .png" required>
                                <div class="form-text">Passeport, CNI ou Permis de conduire. PDF ou Image. Max 5MB.</div>
                                @error('kyc_document')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- PAYMENT SECTION --}}
                            <div class="bg-lighter p-3 rounded mb-4">
                                <h6 class="mb-2">Paiement via Wallet</h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted d-block">Solde actuel</small>
                                        <span
                                            class="fw-bold {{ ($wallet->balance ?? 0) < 5000 ? 'text-danger' : 'text-success' }}">
                                            {{ number_format($wallet->balance ?? 0, 0, ',', ' ') }} FCFA
                                        </span>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted d-block">Coût Licence</small>
                                        <span class="fw-bold">5 000 FCFA</span>
                                    </div>
                                </div>

                                @if (($wallet->balance ?? 0) < 5000)
                                    <div class="alert alert-danger mt-3 mb-0 py-2">
                                        <i class="ti ti-alert-triangle ti-xs me-1"></i> Solde insuffisant.
                                        <a href="{{ route('wallet.recharge') }}" class="fw-bold">Recharger maintenant</a>
                                    </div>
                                @endif
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary"
                                    {{ ($wallet->balance ?? 0) < 5000 ? 'disabled' : '' }}>
                                    <i class="ti ti-check me-1"></i> Payer 5000 FCFA et Soumettre la Demande
                                </button>
                                <a href="{{ route('profile.show') }}" class="btn btn-label-secondary">Annuler</a>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

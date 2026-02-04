@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Recharger mon Wallet')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Wallet /</span> Rechargement
</h4>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card mb-4">
            <h5 class="card-header text-center pb-0">
                <i class='bx bxs-wallet'></i> Recharger votre Wallet
            </h5>
            <div class="card-body">
                <!-- Current Balance -->
                <div class="text-center mb-4">
                    <div class="avatar avatar-xl bg-label-warning mb-2 mx-auto">
                        <span class="avatar-initial rounded"><i class="ti ti-wallet ti-lg"></i></span>
                    </div>
                    <h4 class="mb-1">Solde Actuel: <span class="text-warning">{{ number_format($wallet->balance, 0, ',', ' ') }} FCFA</span></h4>
                    <p class="text-muted">Rechargez via Mobile Money pour acheter sur la marketplace</p>
                </div>

                @if ($errors->any())
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <strong>Erreur!</strong> {{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <form id="rechargeForm" method="POST" action="{{ route('wallet.process-recharge') }}">
                    @csrf

                    <!-- Amount Input -->
                    <x-feature-section feature="wallet.recharge.form-amount">
                        <div class="mb-4">
                            <label class="form-label fw-bold" for="amount">
                                <i class='bx bx-money'></i> Montant à Recharger
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">FCFA</span>
                                <input type="number" id="amount" name="amount" class="form-control" 
                                       placeholder="Ex: 5000" required min="100" step="100" value="5000" />
                            </div>
                            <div class="form-text">Montant minimum: 100 FCFA</div>
                        </div>
                    </x-feature-section>

                    <!-- Provider Selection -->
                    <x-feature-section feature="wallet.recharge.form-provider">
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class='bx bxs-phone'></i> Opérateur Mobile Money
                            </label>
                            <div class="row g-3">
                                <div class="col-6 col-md-3">
                                    <input type="radio" class="btn-check" name="provider" id="airtel" value="airtel" checked>
                                    <label class="btn btn-outline-danger w-100 py-3" for="airtel">
                                        <i class='bx bxs-phone-call bx-md d-block mb-2'></i>
                                        <strong>Airtel</strong><br>
                                        <small class="text-muted">Money</small>
                                    </label>
                                </div>
                                <div class="col-6 col-md-3">
                                    <input type="radio" class="btn-check" name="provider" id="vodacom" value="vodacom">
                                    <label class="btn btn-outline-danger w-100 py-3" for="vodacom">
                                        <i class='bx bxs-phone-call bx-md d-block mb-2'></i>
                                        <strong>M-Pesa</strong><br>
                                        <small class="text-muted">Vodacom</small>
                                    </label>
                                </div>
                                <div class="col-6 col-md-3">
                                    <input type="radio" class="btn-check" name="provider" id="orange" value=" orange">
                                    <label class="btn btn-outline-warning w-100 py-3" for="orange">
                                        <i class='bx bxs-phone-call bx-md d-block mb-2'></i>
                                        <strong>Orange</strong><br>
                                        <small class="text-muted">Money</small>
                                    </label>
                                </div>
                                <div class="col-6 col-md-3">
                                    <input type="radio" class="btn-check" name="provider" id="africell" value="africell">
                                    <label class="btn btn-outline-primary w-100 py-3" for="africell">
                                        <i class='bx bxs-phone-call bx-md d-block mb-2'></i>
                                        <strong>Africell</strong><br>
                                        <small class="text-muted">Money</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </x-feature-section>

                    <!-- Phone Number -->
                    <x-feature-section feature="wallet.recharge.form-phone">
                    <div class="mb-4">
                        <label class="form-label fw-bold" for="phone">
                            <i class='bx bx-phone'></i> Numéro de Téléphone
                        </label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text">+243</span>
                            <input type="text" id="phone" name="phone" class="form-control" 
                                   placeholder="970000000" required maxlength="10" />
                        </div>
                        <div class="form-text">Format: 0XXXXXXXXX (10 chiffres)</div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-4">
                        <button type="submit" class="btn btn-success btn-lg d-grid w-100" id="submitBtn">
                            <i class='bx bx-check-circle'></i> Confirmer le Rechargement
                        </button>
                        <a href="{{ route('profile.show') }}" class="btn btn-label-secondary d-grid w-100 mt-2">
                            <i class='bx bx-x'></i> Annuler
                        </a>
                    </x-feature-section>
                </form>

                <!-- Test Numbers Info -->
                <div class="mt-4">
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class='bx bx-test-tube'></i> <strong>Numéros de Test (Simulateur)</strong>
                        </h6>
                        <p class="mb-2">Utilisez ces numéros pour tester différents scénarios:</p>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="mb-0">
                                    <li><code>+243 97 000 0000</code> - ✅ <span class="badge bg-success">Succès</span> (Airtel)</li>
                                    <li><code>+243 81 000 0001</code> - ❌ <span class="badge bg-danger">Solde insuffisant</span> (M-Pesa)</li>
                                    <li><code>+243 84 000 0002</code> - ⚠️ <span class="badge bg-warning">Erreur réseau</span> (Orange)</li>
                                    <li><code>+243 90 000 0003</code> - 🔐 <span class="badge bg-secondary">OTP incorrect</span> (Africell)</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="mb-0">
                                    <li><code>+243 97 000 0004</code> - 📊 <span class="badge bg-dark">Limite quotidienne</span> (Airtel)</li>
                                    <li><code>+243 81 000 0005</code> - 🔒 <span class="badge bg-dark">Compte bloqué</span> (M-Pesa)</li>
                                    <li><code>+243 84 000 0666</code> - 🐌 <span class="badge bg-info">Réseau lent (5s)</span> (Orange)</li>
                                </ul>
                            </div>
                        </div>
                        <hr class="my-2">
                        <small class="text-muted">
                            <i class='bx bx-info-circle'></i> Les paiements sont <strong>simulés</strong>. 
                            Délai réseau: 1-5 secondes selon le scénario. Frais opérateur: 1.5-2%.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('rechargeForm');
    const submitBtn = document.getElementById('submitBtn');
    const phoneInput = document.getElementById('phone');
    
    // Auto-update phone prefix based on selected provider
    document.querySelectorAll('input[name="provider"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const prefixes = {
                'airtel': '097',
                'vodacom': '081',
                'orange': '084',
                'africell': '090'
            };
            
            const prefix = prefixes[this.value];
            if (phoneInput.value === '' || phoneInput.value.startsWith('0')) {
                phoneInput.placeholder = `${prefix}0000000`;
            }
        });
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Disable submit button and show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Traitement en cours...';
        
        // Show processing alert
        Swal.fire({
            title: 'Traitement du paiement...',
            html: 'Connexion au serveur Mobile Money en cours.<br><small class="text-muted">Cela peut prendre 1-5 secondes</small>',
            icon: 'info',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Submit form
        form.submit();
    });
});
</script>
@endsection

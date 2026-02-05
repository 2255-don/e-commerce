@extends('layouts.layoutMaster')

@section('title', 'Recharger mon Wallet')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endsection

@section('page-style')
<link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
@endsection

@section('content')
<div class="page-header-brand animate-fade-in-up">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="page-title-brand">
                <i class='bx bxs-wallet text-brand-gold'></i>
                Recharger mon Wallet
            </h2>
            <p class="page-subtitle-brand">Rechargez via Mobile Money pour acheter sur la marketplace</p>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Balance Card -->
        <div class="card-stat mb-4 animate-fade-in-up" style="animation-delay: 0.1s; border: 2px solid var(--brand-gold-light);">
            <div class="text-center">
                <div class="stat-icon mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2.5rem;">
                    <i class='bx bxs-wallet'></i>
                </div>
                <p class="stat-label">Solde Actuel</p>
                <h2 class="stat-value" style="font-size: 2.5rem;">{{ number_format($wallet->balance, 0, ',', ' ') }} FCFA</h2>
            </div>
        </div>

        <!-- Recharge Form -->
        <div class="card-brand animate-fade-in-up" style="animation-delay: 0.2s;">
            <div class="card-header card-header-brand" style="background: var(--gradient-gold); color: white;">
                <h5 class="mb-0">
                    <i class='bx bx-money'></i>
                    Formulaire de Rechargement
                </h5>
            </div>
            <div class="card-body p-4">
                @if ($errors->any())
                <div class="alert-brand-warning mb-4">
                    <strong>Erreur!</strong> {{ $errors->first() }}
                </div>
                @endif

                <form id="rechargeForm" method="POST" action="{{ route('wallet.process-recharge') }}">
                    @csrf

                    <!-- Amount Input -->
                    <x-feature-section feature="wallet.recharge.form-amount">
                        <div class="mb-4">
                            <label class="form-label-brand" for="amount">
                                <i class='bx bx-money'></i> Montant à Recharger
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text" style="background: var(--gradient-gold-subtle); border-color: var(--brand-gold);">FCFA</span>
                                <input type="number" id="amount" name="amount" class="form-control-brand" 
                                       placeholder="Ex: 5000" required min="100" step="100" value="5000" />
                            </div>
                            <div class="form-text">Montant minimum: 100 FCFA</div>
                        </div>
                    </x-feature-section>

                    <!-- Provider Selection -->
                    <x-feature-section feature="wallet.recharge.form-provider">
                        <div class="mb-4">
                            <label class="form-label-brand" for="provider">
                                <i class='bx bx-mobile'></i> Opérateur Mobile Money
                            </label>
                            <div class="row g-3">
                                @foreach(['Airtel', 'Vodacom', 'Orange', 'Africell'] as $provider)
                                <div class="col-6 col-md-3">
                                    <input type="radio" class="btn-check" name="provider" id="provider{{ $provider }}" value="{{ strtolower($provider) }}" {{ $loop->first ? 'checked' : '' }}>
                                    <label class="btn btn-outline-gold w-100 py-3" for="provider{{ $provider }}" style="border: 2px solid var(--brand-gold); color: var(--brand-gold);">
                                        <i class='bx bx-mobile-alt d-block fs-3 mb-1'></i>
                                        <strong>{{ $provider }}</strong>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </x-feature-section>

                    <!-- Phone Number -->
                    <x-feature-section feature="wallet.recharge.form-phone">
                        <div class="mb-4">
                            <label class="form-label-brand" for="phone_number">
                                <i class='bx bx-phone'></i> Numéro de Téléphone
                            </label>
                            <input type="tel" id="phone_number" name="phone_number" class="form-control-brand" 
                                   placeholder="+243 XXX XXX XXX" required 
                                   pattern="^\+?[0-9]{10,15}$" />
                            <div class="form-text">Format international accepté (ex: +243...)</div>
                        </div>
                    </x-feature-section>

                    <!-- Submit Button -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-brand-primary btn-lg">
                            <i class='bx bx-check-circle'></i>
                            Valider le Rechargement
                        </button>
                        <small class="text-center text-muted">
                            <i class='bx bx-shield'></i>
                            Paiement sécurisé via Mobile Money
                        </small>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Card -->
        <div class="alert-brand-info mt-4 animate-fade-in-up" style="animation-delay: 0.3s;">
            <h6 class="mb-2">
                <i class='bx bx-info-circle'></i>
                Comment ça marche ?
            </h6>
            <ol class="mb-0 ps-3">
                <li>Choisissez votre montant et opérateur</li>
                <li>Entrez votre numéro Mobile Money</li>
                <li>Validez la transaction sur votre téléphone</li>
                <li>Votre wallet est rechargé instantanément !</li>
            </ol>
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
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Traitement...',
            text: 'Veuillez patienter',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        this.submit();
    });
});
</script>
@endsection

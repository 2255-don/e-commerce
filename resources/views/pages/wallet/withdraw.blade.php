@extends('layouts/layoutMaster')

@section('title', 'Retrait de Fonds')

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Portefeuille /</span> Retrait
    </h4>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Effectuer un Retrait</h5>
                    <small class="text-muted float-end">Solde: {{ number_format($wallet->balance, 0, ',', ' ') }}
                        {{ $wallet->currency }}</small>
                </div>
                <div class="card-body">


                    <form method="POST" action="{{ route('wallet.process-withdraw') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label" for="phone">Numéro Mobile Money</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text">CDF (+243)</span>
                                <input type="text" id="phone" name="phone" class="form-control"
                                    placeholder="099..." required />
                            </div>
                            <div class="form-text">Vodacom, Airtel, Orange, Africell supportés.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="amount">Montant à retirer (Minimum 500)</label>
                            <div class="input-group input-group-merge">
                                <input type="number" id="amount" name="amount" class="form-control" placeholder="1000"
                                    min="500" max="{{ $wallet->balance }}" required />
                                <span class="input-group-text">{{ $wallet->currency }}</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Confirmer le Retrait</button>
                    </form>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card">
                <div class="card-body">
                    <h6><i class="ti ti-info-circle me-1"></i> Informations</h6>
                    <ul class="ps-3 mb-0 small text-muted">
                        <li>Le montant sera déduit immédiatement de votre portefeuille.</li>
                        <li>Le transfert mobile money est simulé pour le test.</li>
                        <li>Numéros de test pour erreurs: 0...0001 (Solde insuffisant), 0...0002 (Erreur Réseau).</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
@endsection

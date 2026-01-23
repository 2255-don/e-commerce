@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Recharger mon Wallet')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Wallet /</span> Rechargement
</h4>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card mb-4">
            <h5 class="card-header text-center pb-0">Recharger votre solde</h5>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="avatar avatar-xl bg-label-success mb-2 mx-auto">
                        <span class="avatar-initial rounded"><i class="ti ti-wallet ti-lg"></i></span>
                    </div>
                    <h4 class="mb-1">Solde Actuel : {{ number_format($wallet->balance, 0, ',', ' ') }} FCFA</h4>
                </div>

                @if ($errors->any())
                <div class="alert alert-danger alert-dismissible" role="alert">
                    {{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <form id="rechargeForm" method="POST" action="{{ route('wallet.process-recharge') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="amount">Montant à recharger (FCFA)</label>
                        <div class="input-group">
                            <span class="input-group-text">FCFA</span>
                            <input type="number" id="amount" name="amount" class="form-control" placeholder="Ex: 5000" required min="100" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="phone">Numéro Mobile Money (Simulateur)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ti ti-phone"></i></span>
                            <input type="text" id="phone" name="phone" class="form-control" placeholder="Entrez le numéro magique" required />
                        </div>
                        <div class="form-text mt-2">
                             <span class="badge bg-label-secondary">Astuce Test :</span><br>
                             - <code>80000000</code> : Succès<br>
                             - <code>80000001</code> : Solde insuffisant<br>
                             - <code>80000002</code> : Erreur réseau
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-success d-grid w-100">Confirmer le rechargement</button>
                        <a href="{{ route('profile.show') }}" class="btn btn-label-secondary d-grid w-100 mt-2">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

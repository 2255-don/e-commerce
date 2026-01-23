@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Soumission KYC')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Utilisateur /</span> Validation KYC
</h4>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card mb-4">
            <h5 class="card-header">Vérification de l'Identité</h5>
            <div class="card-body">
                <div class="alert alert-warning mb-4" role="alert">
                    <h6 class="alert-heading mb-1">Pourquoi est-ce nécessaire ?</h6>
                    <span>Pour pouvoir vendre sur notre plateforme, nous devons vérifier votre identité conformément aux réglementations en vigueur.</span>
                </div>

                @if ($errors->any())
                <div class="alert alert-danger alert-dismissible" role="alert">
                    {{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if($user->kyc_status === 'pending')
                    <div class="text-center py-5">
                        <div class="avatar avatar-xl bg-label-warning mb-3 mx-auto">
                            <span class="avatar-initial rounded"><i class="ti ti-clock ti-lg"></i></span>
                        </div>
                        <h4>Demande en cours...</h4>
                        <p class="text-muted">Votre document a été soumis et est en attente de validation par un administrateur.</p>
                        <a href="{{ route('profile.show') }}" class="btn btn-primary mt-3">Retour au profil</a>
                    </div>
                @elseif($user->kyc_status === 'verified')
                    <div class="text-center py-5">
                        <div class="avatar avatar-xl bg-label-success mb-3 mx-auto">
                            <span class="avatar-initial rounded"><i class="ti ti-check ti-lg"></i></span>
                        </div>
                        <h4>Identité Vérifiée</h4>
                        <p class="text-muted">Votre compte est entièrement validé. Vous pouvez continuer vos activités.</p>
                        <a href="{{ route('profile.show') }}" class="btn btn-primary mt-3">Retour au profil</a>
                    </div>
                @else
                    <form id="kycForm" method="POST" action="{{ route('kyc.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="shop_name" class="form-label">Nom de votre boutique / shop</label>
                            <input type="text" class="form-control" id="shop_name" name="shop_name" placeholder="Ex: Ma Super Boutique" value="{{ old('shop_name', $user->sellerProfile->shop_name) }}" required>
                        </div>

                        <div class="mb-4">
                            <label for="kyc_document" class="form-label">Pièce d'identité (CNI, Passeport, Permis)</label>
                            <input class="form-control" type="file" id="kyc_document" name="kyc_document" accept="image/*" required>
                            <div class="form-text mt-2 text-info">
                                <i class="ti ti-info-circle ti-xs"></i> Formats autorisés : JPG, PNG. Taille max : 2Mo.
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary w-100 mb-2">Soumettre mon document</button>
                            <a href="{{ route('profile.show') }}" class="text-muted small">Annuler et retourner au profil</a>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

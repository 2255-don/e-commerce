@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Profil Utilisateur')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Paramètres /</span> Compte
</h4>

<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-pills flex-column flex-md-row mb-4">
            <li class="nav-item"><a class="nav-item nav-link active" href="javascript:void(0);"><i class="ti-xs ti ti-user-check me-1"></i> Compte</a></li>
        </ul>
        <div class="card mb-4">
            <h5 class="card-header">Détails du profil</h5>
            <!-- Account -->
            <div class="card-body">
                <div class="d-flex align-items-start align-items-sm-center gap-4">
                    <img src="{{ $user->profile_photo_url }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar" />
                    <div class="button-wrapper">
                        <label for="upload" class="btn btn-primary me-2 mb-3" tabindex="0">
                            <span class="d-none d-sm-block">Télécharger une nouvelle photo</span>
                            <i class="ti ti-upload d-block d-sm-none"></i>
                            <input type="file" id="upload" class="account-file-input" hidden accept="image/png, image/jpeg" />
                        </label>
                        <button type="button" class="btn btn-label-secondary account-image-reset mb-3">
                            <i class="ti ti-refresh-dot d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Réinitialiser</span>
                        </button>
                        <div class="text-muted">Autorisé JPG, GIF ou PNG. Taille maximale de 800Ko</div>
                    </div>
                </div>
            </div>
            <hr class="my-0">
            <div class="card-body">
                @if (session('status') === 'profile-updated')
                <div class="alert alert-success alert-dismissible" role="alert">
                    Profil mis à jour avec succès !
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if ($errors->any())
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <form id="formAccountSettings" method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Nom complet</label>
                            <input class="form-control" type="text" id="name" name="name" value="{{ old('name', $user->name) }}" autofocus />
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="email" class="form-label">E-mail</label>
                            <input class="form-control" type="text" id="email" name="email" value="{{ old('email', $user->email) }}" placeholder="john.doe@example.com" />
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label" for="phone_number">Numéro de téléphone</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text">FR (+33)</span>
                                <input type="text" id="phone_number" name="phone_number" class="form-control" value="{{ old('phone_number', $user->phone_number) }}" placeholder="06 12 34 56 78" />
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    <h5 class="mb-4">Changer le mot de passe (optionnel)</h5>
                    
                    <div class="row">
                        <div class="mb-3 col-md-6 form-password-toggle">
                            <label class="form-label" for="password">Nouveau mot de passe</label>
                            <div class="input-group input-group-merge">
                                <input class="form-control" type="password" id="password" name="password" placeholder="············" />
                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                            </div>
                        </div>
                        <div class="mb-3 col-md-6 form-password-toggle">
                            <label class="form-label" for="password_confirmation">Confirmer le nouveau mot de passe</label>
                            <div class="input-group input-group-merge">
                                <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" placeholder="············" />
                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-2">Enregistrer les modifications</button>
                        <button type="reset" class="btn btn-label-secondary">Annuler</button>
                    </div>
                </form>
            </div>
            <!-- /Account -->
        </div>
        <div class="card">
            <h5 class="card-header">Supprimer le compte</h5>
            <div class="card-body">
                <div class="mb-3 col-12 mb-0">
                    <div class="alert alert-warning">
                        <h5 class="alert-heading mb-1">Êtes-vous sûr de vouloir supprimer votre compte ?</h5>
                        <p class="mb-0">Une fois que vous supprimez votre compte, il n'y a pas de retour en arrière. S'il vous plaît soyez certain.</p>
                    </div>
                </div>
                <form id="formAccountDeactivation" onsubmit="return false">
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="accountActivation" id="accountActivation" />
                        <label class="form-check-label" for="accountActivation">Je confirme la désactivation de mon compte</label>
                    </div>
                    <button type="submit" class="btn btn-danger deactivate-account">Désactiver le compte</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

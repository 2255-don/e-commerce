@extends('layouts/layoutMaster')

@section('title', 'Mon Profil')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/animate-css/animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endsection

@section('page-style')
    <style>
        :root {
            --gold-primary: #D4AF37;
            --gold-light: #F4E5B0;
            --gold-dark: #B5952F;
            --navy-dark: #1a1a1a;
            --navy-light: #2c2c2c;
        }

        body {
            background-color: #f8f9fa;
        }

        /* Premium Card */
        .premium-card {
            background: #fff;
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }

        .premium-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(212, 175, 55, 0.1);
        }

        /* Typography & Accents */
        .text-gold {
            color: var(--gold-primary) !important;
        }

        .bg-gold {
            background-color: var(--gold-primary) !important;
            color: white;
        }

        .page-title {
            font-weight: 800;
            color: var(--navy-dark);
            position: relative;
        }

        .section-header {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--navy-light);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #eee;
            display: flex;
            align-items: center;
        }

        .section-header i {
            color: var(--gold-primary);
            margin-right: 0.75rem;
        }

        /* Forms & Buttons */
        .form-control:focus,
        .form-select:focus {
            border-color: var(--gold-primary);
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.15);
        }

        .btn-gold {
            background: linear-gradient(135deg, var(--gold-primary) 0%, var(--gold-dark) 100%);
            color: white;
            border: none;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
            transition: all 0.3s ease;
        }

        .btn-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(212, 175, 55, 0.4);
            color: white;
        }

        .btn-outline-gold {
            border: 2px solid var(--gold-primary);
            color: var(--gold-dark);
            background: transparent;
            font-weight: 600;
        }

        .btn-outline-gold:hover {
            background: var(--gold-primary);
            color: white;
        }

        /* Badges */
        .badge-status {
            padding: 0.5em 1em;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .badge-gold {
            background-color: rgba(212, 175, 55, 0.1);
            color: var(--gold-dark);
            border: 1px solid rgba(212, 175, 55, 0.3);
        }
    </style>
@endsection

@section('content')
    <div class="animate__animated animate__fadeIn">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="page-title mb-1">Mon Profil</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-muted">Tableau de
                                bord</a></li>
                        <li class="breadcrumb-item active text-gold" aria-current="page">Compte</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Messages -->
        @if (session('status') === 'profile-updated')
            <div class="alert alert-success alert-dismissible animate__animated animate__fadeInDown" role="alert">
                <i class="ti ti-check me-2"></i> Profil mis à jour avec succès !
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible animate__animated animate__shakeX" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">

            <!-- Left Column: User Card & Actions -->
            <div class="col-lg-4 mb-4">

                <!-- Profile Card -->
                <div class="premium-card p-4 text-center mb-4">
                    <div class="position-relative d-inline-block mb-3">
                        <img src="{{ $user->profile_photo_url }}" alt="user-avatar"
                            class="rounded-circle img-fluid border border-3 border-white shadow"
                            style="width: 120px; height: 120px; object-fit: cover;" id="uploadedAvatar" />
                        <label for="upload"
                            class="position-absolute bottom-0 end-0 bg-gold rounded-circle p-2 shadow cursor-pointer"
                            style="transform: translate(10%, 10%);" title="Changer la photo">
                            <i class="ti ti-camera text-white"></i>
                            <input type="file" id="upload" class="account-file-input" hidden
                                accept="image/png, image/jpeg" />
                        </label>
                    </div>

                    <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                    <p class="text-muted mb-3">{{ $user->email }}</p>

                    <div class="d-flex justify-content-center gap-2 mb-4">
                        <!-- Status Badges -->
                        @if ($user->isSuperAdmin())
                            <span class="badge-status bg-gold text-white" title="Administrateur">
                                <i class="ti ti-crown me-1"></i> Super Admin
                            </span>
                        @else
                            @if ($user->kyc_status === 'verified')
                                <span class="badge-status badge-gold" title="Identité Vérifiée">
                                    <i class="ti ti-shield-check me-1"></i> Vérifié
                                </span>
                            @elseif($user->kyc_status === 'pending')
                                <span class="badge-status bg-label-warning" title="En attente de validation">
                                    <i class="ti ti-clock me-1"></i> KYC En cours
                                </span>
                            @else
                                <a href="{{ route('kyc.form') }}" class="badge-status bg-label-info text-decoration-none">
                                    <i class="ti ti-id me-1"></i> Vérifier l'identité
                                </a>
                            @endif
                        @endif

                        @if ($user->sellerProfile && $user->sellerProfile->isLicenseActive())
                            @php
                                $daysRemaining = now()->diffInDays($user->sellerProfile->licence_expire_at, false);
                                $daysRemaining = (int) $daysRemaining; // Ensure integer
                            @endphp

                            <div class="d-flex flex-column align-items-center gap-2">
                                <span class="badge-status bg-label-primary"
                                    title="Expire le {{ $user->sellerProfile->licence_expire_at->format('d/m/Y') }}">
                                    <i class="ti ti-shopping-bag me-1"></i> Vendeur ({{ $daysRemaining }} j restants)
                                </span>

                                @if ($daysRemaining <= 3)
                                    <a href="{{ route('seller.license') }}"
                                        class="btn btn-xs btn-gold shadow-sm animate__animated animate__pulse animate__infinite">
                                        <i class="ti ti-refresh me-1"></i> Renouveler
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Wallet Card Mini -->
                    <div class="bg-lighter rounded p-3 mb-3">
                        <small class="text-muted text-uppercase fw-bold d-block mb-1">Portefeuille</small>
                        <h4 class="text-gold fw-bolder mb-2">{{ number_format($user->wallet->balance ?? 0, 0, ',', ' ') }}
                            FCFA</h4>
                        <a href="{{ route('wallet.recharge') }}" class="btn btn-sm btn-outline-gold w-100">
                            <i class="ti ti-plus me-1"></i> Recharger
                        </a>
                    </div>

                    @if (!($user->sellerProfile && $user->sellerProfile->isLicenseActive()))
                        @if (!$user->isSuperAdmin())
                            <a href="{{ route('seller.license') }}" class="btn btn-gold w-100 shadow-sm">
                                <i class="ti ti-star me-2"></i> Devenir Vendeur (5000 FCFA)
                            </a>
                        @endif
                    @endif

                    <!-- Hidden reset button logic kept for compatibility -->
                    <button type="button" class="btn btn-label-secondary account-image-reset d-none">Reset</button>
                </div>

                <!-- Delete Account (Moved here for better hierarchy) -->
                <div class="premium-card p-4 border-start border-danger border-4">
                    <div class="section-header text-danger border-0 mb-2">
                        <i class="ti ti-alert-triangle text-danger"></i> Zone de Danger
                    </div>
                    <p class="small text-muted mb-3">La suppression de votre compte est irréversible. Toutes vos données
                        seront perdues.</p>
                    <button type="button" class="btn btn-label-danger w-100" data-bs-toggle="modal"
                        data-bs-target="#deleteAccountModal">
                        Supprimer mon compte
                    </button>
                </div>
            </div>

            <!-- Right Column: Settings Forms -->
            <div class="col-lg-8">
                <div class="premium-card p-4 h-100">

                    <form id="formAccountSettings" method="POST" action="{{ route('profile.update') }}">
                        @csrf

                        <!-- Personal Info Section -->
                        <div class="mb-5">
                            <div class="section-header">
                                <i class="ti ti-user"></i> Informations Personnelles
                            </div>

                            <x-feature-section feature="user.profile.basic-info">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Nom complet</label>
                                        <input class="form-control" type="text" id="name" name="name"
                                            value="{{ old('name', $user->name) }}" placeholder="Votre nom" />
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">E-mail</label>
                                        <input class="form-control" type="text" id="email" name="email"
                                            value="{{ old('email', $user->email) }}" placeholder="email@exemple.com" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="phone_number">Numéro de téléphone</label>
                                        <input type="text" id="phone_number" name="phone_number" class="form-control"
                                            value="{{ old('phone_number', $user->phone_number) }}"
                                            placeholder="+33 6 12 34 56 78" />
                                    </div>
                                </div>

                                @if ($user->kyc_status === 'verified' && $user->sellerProfile && $user->sellerProfile->shop_name)
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <label for="shop_name" class="form-label">Nom de la boutique <span
                                                    class="text-gold">*</span></label>
                                            <input class="form-control" type="text" id="shop_name" name="shop_name"
                                                value="{{ old('shop_name', $user->sellerProfile->shop_name) }}" />
                                            <div class="form-text">Ce nom sera affiché sur vos produits.</div>
                                        </div>
                                    </div>
                                @endif
                            </x-feature-section>
                        </div>

                        <!-- Security Section -->
                        <div class="mb-4">
                            <div class="section-header">
                                <i class="ti ti-lock"></i> Sécurité
                            </div>

                            <x-feature-section feature="user.profile.change-password">
                                <div class="row g-3">
                                    <div class="col-md-6 form-password-toggle">
                                        <label class="form-label" for="password">Nouveau mot de passe</label>
                                        <div class="input-group input-group-merge">
                                            <input class="form-control" type="password" id="password" name="password"
                                                placeholder="············" />
                                            <span class="input-group-text cursor-pointer"><i
                                                    class="ti ti-eye-off"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 form-password-toggle">
                                        <label class="form-label" for="password_confirmation">Confirmer le mot de
                                            passe</label>
                                        <div class="input-group input-group-merge">
                                            <input class="form-control" type="password" id="password_confirmation"
                                                name="password_confirmation" placeholder="············" />
                                            <span class="input-group-text cursor-pointer"><i
                                                    class="ti ti-eye-off"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </x-feature-section>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-5">
                            <button type="reset" class="btn btn-label-secondary">Annuler</button>
                            <button type="submit" class="btn btn-gold px-4">Enregistrer</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Delete Account (Kept functionality separate form UI) -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning mb-0">
                        <h6 class="alert-heading fw-bold mb-1">Êtes-vous absolument sûr ?</h6>
                        <p class="mb-0">Cette action ne peut pas être annulée. Cela supprimera définitivement votre
                            compte et retirera vos données de nos serveurs.</p>
                    </div>
                    <form id="formAccountDeactivation" onsubmit="return false" class="mt-3">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="accountActivation"
                                id="accountActivation" />
                            <label class="form-check-label" for="accountActivation">Je confirme la suppression
                                définitive</label>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-label-secondary"
                                data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-danger deactivate-account" disabled>Supprimer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/cleavejs/cleave.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Logic for delete account checklist
            const checkbox = document.getElementById('accountActivation');
            const deleteBtn = document.querySelector('.deactivate-account');

            if (checkbox && deleteBtn) {
                checkbox.addEventListener('change', function() {
                    deleteBtn.disabled = !this.checked;
                });
            }

            // Logic for file input (image preview) - kept from template
            // (Assuming standard template JS handles the actual upload/preview logic using 'account-file-input' and 'account-image-reset' classes)
        });
    </script>
    <script src="{{ asset('assets/js/pages-account-settings-account.js') }}"></script>
@endsection

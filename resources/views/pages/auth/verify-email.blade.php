@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/blankLayout')

@section('title', 'Verify Email')

@section('page-style')
<!-- Page -->
<link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">
@endsection

@section('content')
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4">
            <!-- Verify Email -->
            <div class="card">
                <div class="card-body">
                    <!-- Logo -->
                    <div class="app-brand justify-content-center mb-4 mt-2">
                        <a href="{{ url('/') }}" class="app-brand-link gap-2">
                            <span class="app-brand-logo">
                                <img src="{{ asset('assets/img/branding/logo.png') }}" alt="Logo" style="max-width: 100%; height: 80px; object-fit: contain;">
                            </span>
                        </a>
                    </div>
                    <!-- /Logo -->
                    <h4 class="mb-1 pt-2">Vérifiez votre email ✉️</h4>
                    <p class="text-start mb-4">
                        Un lien de validation de compte a été envoyé à votre adresse e-mail. Veuillez suivre le lien à l'intérieur pour continuer.
                    </p>

                    @if (session('status') == 'verification-link-sent')
                    <div class="alert alert-success" role="alert">
                        Un nouveau lien de vérification a été envoyé à l'adresse e-mail que vous avez fournie lors de l'inscription.
                    </div>
                    @endif

                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100 mb-3">Renvoyer l'e-mail de vérification</button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-link w-100">Déconnexion</button>
                    </form>
                </div>
            </div>
            <!-- /Verify Email -->
        </div>
    </div>
</div>
@endsection

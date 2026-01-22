@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/blankLayout')

@section('title', 'Two Factor Challenge')

@section('page-style')
<!-- Page -->
<link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">
@endsection

@section('content')
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4">
            <!-- Two Factor Challenge -->
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
                    <h4 class="mb-1 pt-2">Vérification en deux étapes 🛡️</h4>
                    <p class="text-start mb-4">
                        Veuillez confirmer l'accès à votre compte en saisissant le code d'authentification fourni par votre application d'authentification ou l'un de vos codes de secours.
                    </p>

                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div x-data="{ recovery: false }">
                        <form method="POST" action="{{ route('two-factor.login') }}">
                            @csrf

                            <div class="mb-3" x-show="! recovery">
                                <label for="code" class="form-label">Code</label>
                                <input class="form-control" type="text" id="code" name="code" autofocus x-bind:disabled="recovery" autocomplete="one-time-code" />
                            </div>

                            <div class="mb-3" x-show="recovery" style="display: none;">
                                <label for="recovery_code" class="form-label">Code de secours</label>
                                <input class="form-control" type="text" id="recovery_code" name="recovery_code" x-bind:disabled="! recovery" autocomplete="one-time-code" />
                            </div>

                            <div class="d-flex justify-content-end mb-3">
                                <button type="button" class="btn btn-outline-secondary btn-sm" x-show="! recovery" x-on:click="recovery = true">
                                    Utiliser un code de secours
                                </button>

                                <button type="button" class="btn btn-outline-secondary btn-sm" x-show="recovery" x-on:click="recovery = false" style="display: none;">
                                    Utiliser un code d'authentification
                                </button>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Connexion</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /Two Factor Challenge -->
        </div>
    </div>
</div>
@endsection

@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Gestion des KYC')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Administration /</span> Validation KYC
</h4>

@if(session('status'))
    <div class="alert alert-success alert-dismissible mb-4" role="alert">
        {{ session('status') == 'kyc-approved' ? 'Demande validée avec succès !' : 'Demande rejetée.' }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- En attente Section -->
<div class="card mb-4">
    <div class="card-header border-bottom">
        <h5 class="card-title mb-0">Demandes en attente ({{ $pendingUsers->count() }})</h5>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Email</th>
                    <th>Boutique</th>
                    <th>Document</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse($pendingUsers as $pUser)
                    <tr>
                        <td>
                            <div class="d-flex justify-content-start align-items-center">
                                <div class="avatar-wrapper">
                                    <div class="avatar me-2"><img src="{{ $pUser->profile_photo_url }}" alt="Avatar" class="rounded-circle"></div>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="emp_name text-truncate fw-semibold">{{ $pUser->name }}</span>
                                    <small class="emp_post text-truncate text-muted">Vendeur</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $pUser->email }}</td>
                        <td>{{ $pUser->sellerProfile->shop_name ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ asset('storage/' . $pUser->kyc_document_path) }}" target="_blank" class="btn btn-sm btn-label-info">
                                <i class="ti ti-eye me-1"></i> Voir
                            </a>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <form action="{{ route('admin.kyc.approve', $pUser) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm btn-success">Valider</button>
                                </form>
                                <form action="{{ route('admin.kyc.reject', $pUser) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm btn-danger">Rejeter</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Aucune demande en attente.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Historique Section -->
<div class="card">
    <div class="card-header border-bottom">
        <h5 class="card-title mb-0">Toutes les demandes</h5>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($allUsers as $u)
                    <tr>
                        <td>{{ $u->name }}</td>
                        <td>
                            <span class="badge bg-label-{{ $u->kyc_status === 'verified' ? 'success' : ($u->kyc_status === 'rejected' ? 'danger' : 'warning') }}">
                                {{ strtoupper($u->kyc_status) }}
                            </span>
                        </td>
                        <td>{{ $u->updated_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ asset('storage/' . $u->kyc_document_path) }}" target="_blank" class="text-info"><i class="ti ti-file me-1"></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

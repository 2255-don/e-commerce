@extends('layouts.layoutMaster')

@section('title', 'Gestion des Features')

@section('page-style')
<link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
@endsection

@section('content')
<div class="page-header-brand animate-fade-in-up">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="page-title-brand">
                <i class='bx bxs-cog text-brand-gold'></i>
                Gestion des Features
            </h2>
            <p class="page-subtitle-brand">Gérez les fonctionnalités de l'application</p>
        </div>
        <div>
            <button class="btn btn-brand-primary">
                <i class='bx bx-refresh'></i>
                Scanner les Routes
            </button>
        </div>
    </div>
</div>

<!-- Features by Module -->
@php
    $featuresByModule = $features->groupBy('module.name');
@endphp

@foreach($featuresByModule as $moduleName => $moduleFeatures)
<div class="card-brand mb-4 animate-fade-in-up">
    <div class="card-header card-header-brand" style="background: var(--gradient-gold); color: white;">
        <h5 class="mb-0">
            <i class='bx bx-package'></i>
            {{ $moduleName }}
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-brand">
                <thead>
                    <tr>
                        <th>Feature</th>
                        <th>Route</th>
                        <th>Permissions</th>
                        <th>Actif</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($moduleFeatures as $feature)
                    <tr>
                        <td>
                            <strong>{{ $feature->name }}</strong>
                            <div class="text-muted small">
                                <code>{{ $feature->slug }}</code>
                            </div>
                        </td>
                        <td>
                            <code>{{ $feature->route_name ?? 'N/A' }}</code>
                        </td>
                        <td>
                            @if($feature->permissions()->count() > 0)
                                <span class="badge badge-brand-gold">
                                    {{ $feature->permissions()->count() }} permissions
                                </span>
                            @else
                                <span class="badge badge-brand-grey">Libre</span>
                            @endif
                        </td>
                        <td>
                            @if($feature->is_active)
                                <span class="badge badge-status-active">
                                    <i class='bx bx-check-circle'></i>
                                    Actif
                                </span>
                            @else
                                <span class="badge badge-status-inactive">
                                    Inactif
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endforeach

@if($featuresByModule->isEmpty())
<div class="text-center py-5">
    <i class='bx bx-code-alt' style="font-size: 4rem; color: var(--grey-300);"></i>
    <p class="text-muted mt-3">Aucune feature trouvée</p>
</div>
@endif
@endsection

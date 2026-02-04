@extends('layouts.layoutMaster')

@section('title', 'Gestion des Rôles')

@section('page-style')
<link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
<style>
    .role-card {
        border-radius: var(--radius-lg);
        border: 1px solid var(--grey-200);
        padding: 1.75rem;
        background: white;
        transition: all var(--transition-smooth);
        height: 100%;
    }

    .role-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--shadow-lg);
        border-color: var(--brand-gold-light);
    }

    .role-header {
        display: flex;
        justify-content-between;
        align-items-start;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--grey-100);
    }

    .role-icon {
        width: 55px;
        height: 55px;
        border-radius: var(--radius-md);
        background: var(--gradient-gold);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        box-shadow: var(--shadow-gold);
        flex-shrink: 0;
    }

    .role-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--grey-900);
        margin-bottom: 0.25rem;
    }

    .role-description {
        color: var(--grey-600);
        font-size: 0.875rem;
        line-height: 1.6;
    }

    .role-stats {
        display: flex;
        gap: 1.5rem;
        margin-top: 1rem;
    }

    .role-stat-item {
        text-align: center;
    }

    .role-stat-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--brand-gold);
    }

    .role-stat-label {
        font-size: 0.75rem;
        color: var(--grey-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .role-actions {
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid var(--grey-100);
        display: flex;
        gap: 0.5rem;
    }
</style>
@endsection

@section('content')
<div class="page-header-brand animate-fade-in-up">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="page-title-brand">
                <i class='bx bxs-shield text-brand-gold'></i>
                Gestion des Rôles
            </h2>
            <p class="page-subtitle-brand">Gérez les rôles et leurs permissions</p>
        </div>
        <div>
            <a href="{{ route('admin.roles.create') }}" class="btn btn-brand-primary">
                <i class='bx bx-plus-circle'></i>
                Nouveau Rôle
            </a>
        </div>
    </div>
</div>

<!-- Roles Grid -->
<div class="row g-4">
    @forelse($roles as $index => $role)
    <div class="col-lg-4 col-md-6">
        <div class="role-card animate-fade-in-up" style="animation-delay: {{ $index * 0.1 }}s;">
            <div class="role-header">
                <div class="flex-grow-1">
                    <h4 class="role-title">{{ $role->name }}</h4>
                    <p class="role-description">
                        {{ $role->description ?? 'Aucune description' }}
                    </p>
                </div>
                <div class="role-icon">
                    <i class='bx {{ $role->slug === "admin" ? "bxs-crown" : ($role->slug === "seller" ? "bxs-store" : "bxs-user") }}'></i>
                </div>
            </div>

            <div class="role-stats">
                <div class="role-stat-item">
                    <div class="role-stat-value">
                        {{ $role->users()->count() }}
                    </div>
                    <div class="role-stat-label">Utilisateurs</div>
                </div>
                <div class="role-stat-item">
                    <div class="role-stat-value">
                        {{ $role->permissions()->count() }}
                    </div>
                    <div class="role-stat-label">Permissions</div>
                </div>
            </div>

            <div class="role-actions">
                <a href="{{ route('admin.roles.permissions', $role->id) }}" 
                   class="btn btn-brand-outline flex-grow-1">
                    <i class='bx bx-cog'></i>
                    Permissions
                </a>
                <a href="{{ route('admin.roles.edit', $role->id) }}" 
                   class="btn btn-icon-brand">
                    <i class='bx bx-edit'></i>
                </a>
                @if(!in_array($role->slug, ['admin', 'super-admin']))
                <button class="btn btn-icon-brand" 
                        style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(220, 38, 38, 0.15) 100%); color: #dc2626;">
                    <i class='bx bx-trash'></i>
                </button>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center py-5">
            <i class='bx bx-shield-x' style="font-size: 4rem; color: var(--grey-300);"></i>
            <p class="text-muted mt-3">Aucun rôle trouvé</p>
            <a href="{{ route('admin.roles.create') }}" class="btn btn-brand-primary mt-2">
                <i class='bx bx-plus-circle'></i>
                Créer un rôle
            </a>
        </div>
    </div>
    @endforelse
</div>
@endsection

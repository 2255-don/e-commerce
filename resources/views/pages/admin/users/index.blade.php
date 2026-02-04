@extends('layouts.layoutMaster')

@section('title', 'Gestion des Utilisateurs')

@section('page-style')
<link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
<style>
    .user-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--gradient-gold);
        color: white;
        font-weight: 700;
        font-size: 1.125rem;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .user-details h6 {
        margin: 0;
        font-weight: 700;
        color: var(--grey-900);
    }

    .user-email {
        font-size: 0.875rem;
        color: var(--grey-600);
    }

    .action-buttons {
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
                <i class='bx bxs-user-account text-brand-gold'></i>
                Gestion des Utilisateurs
            </h2>
            <p class="page-subtitle-brand">Gérez les comptes utilisateurs et leurs rôles</p>
        </div>
        <div>
            <a href="{{ route('register') }}" class="btn btn-brand-primary">
                <i class='bx bx-plus-circle'></i>
                Nouvel Utilisateur
            </a>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card-stat animate-fade-in-up" style="animation-delay: 0.1s;">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="stat-label">Total Utilisateurs</p>
                    <h3 class="stat-value">{{ $users->total() }}</h3>
                </div>
                <div class="stat-icon">
                    <i class='bx bxs-group'></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-stat animate-fade-in-up" style="animation-delay: 0.2s;">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="stat-label">Actifs</p>
                    <h3 class="stat-value">{{ $users->where('statut', 'active')->count() }}</h3>
                </div>
                <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <i class='bx bx-check-circle'></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-stat animate-fade-in-up" style="animation-delay: 0.3s;">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="stat-label">Vendeurs</p>
                    <h3 class="stat-value">{{ $users->filter(fn($u) => $u->isSeller())->count() }}</h3>
                </div>
                <div class="stat-icon" style="background: var(--gradient-grey);">
                    <i class='bx bxs-store'></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-stat animate-fade-in-up" style="animation-delay: 0.4s;">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="stat-label">Administrateurs</p>
                    <h3 class="stat-value">{{ $users->filter(fn($u) => $u->hasRole('admin'))->count() }}</h3>
                </div>
                <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <i class='bx bxs-shield'></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="card-brand animate-fade-in-up" style="animation-delay: 0.5s;">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-brand">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Rôles</th>
                        <th>Statut</th>
                        <th>Inscription</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="user-info">
                                <div class="user-avatar">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div class="user-details">
                                    <h6>{{ $user->name }}</h6>
                                    <div class="user-email">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap">
                                @forelse($user->roles as $role)
                                    <span class="badge badge-brand-outline">
                                        <i class='bx bx-shield'></i>
                                        {{ $role->name }}
                                    </span>
                                @empty
                                    <span class="badge badge-brand-grey">Aucun rôle</span>
                                @endforelse
                            </div>
                        </td>
                        <td>
                            @if($user->statut === 'active')
                                <span class="badge badge-status-active">
                                    <i class='bx bx-check-circle'></i>
                                    Actif
                                </span>
                            @else
                                <span class="badge badge-status-inactive">
                                    <i class='bx bx-x-circle'></i>
                                    Inactif
                                </span>
                            @endif
                        </td>
                        <td>
                            <small class="text-muted">
                                <i class='bx bx-calendar'></i>
                                {{ $user->created_at->format('d/m/Y') }}
                            </small>
                        </td>
                        <td>
                            <div class="action-buttons justify-content-end">
                                <a href="{{ route('admin.users.roles', $user->id) }}" 
                                   class="btn btn-icon-brand" 
                                   title="Gérer les rôles">
                                    <i class='bx bx-shield'></i>
                                </a>
                                <button class="btn btn-icon-brand" 
                                        style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(220, 38, 38, 0.15) 100%); color: #dc2626;"
                                        title="Désactiver">
                                    <i class='bx bx-trash'></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class='bx bx-user-x' style="font-size: 3rem; color: var(--grey-300);"></i>
                            <p class="text-muted mt-3 mb-0">Aucun utilisateur trouvé</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

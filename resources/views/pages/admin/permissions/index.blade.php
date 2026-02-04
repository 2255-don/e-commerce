@extends('layouts.layoutMaster')

@section('title', 'Gestion des Permissions')

@section('page-style')
<link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
@endsection

@section('content')
<div class="page-header-brand animate-fade-in-up">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="page-title-brand">
                <i class='bx bxs-lock-alt text-brand-gold'></i>
                Gestion des Permissions
            </h2>
            <p class="page-subtitle-brand">Gérez les permissions d'accès aux features</p>
        </div>
        <div>
            <a href="{{ route('admin.permissions.create') }}" class="btn btn-brand-primary">
                <i class='bx bx-plus-circle'></i>
                Nouvelle Permission
            </a>
        </div>
    </div>
</div>

<!-- Permissions Table -->
<div class="card-brand animate-fade-in-up" style="animation-delay: 0.2s;">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-brand">
                <thead>
                    <tr>
                        <th>Permission</th>
                        <th>Features Associées</th>
                        <th>Rôles</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permissions as $permission)
                    <tr>
                        <td>
                            <div>
                                <strong>{{ $permission->name }}</strong>
                                <div class="text-muted small">
                                    <code>{{ $permission->slug }}</code>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-brand-gold">
                                <i class='bx bx-code-alt'></i>
                                {{ $permission->features()->count() }} features
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap">
                                @forelse($permission->roles as $role)
                                    <span class="badge badge-brand-outline">{{ $role->name }}</span>
                                @empty
                                    <span class="text-muted small">Aucun rôle</span>
                                @endforelse
                            </div>
                        </td>
                        <td>
                            <div class="action-buttons justify-content-end">
                                <a href="{{ route('admin.permissions.edit', $permission->id) }}" 
                                   class="btn btn-icon-brand">
                                    <i class='bx bx-edit'></i>
                                </a>
                                <button class="btn btn-icon-brand" 
                                        style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(220, 38, 38, 0.15) 100%); color: #dc2626;">
                                    <i class='bx bx-trash'></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <i class='bx bx-lock-open' style="font-size: 3rem; color: var(--grey-300);"></i>
                            <p class="text-muted mt-3 mb-0">Aucune permission trouvée</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

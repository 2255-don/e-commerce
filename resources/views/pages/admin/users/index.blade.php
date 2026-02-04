@extends('layouts/layoutMaster')

@section('title', __('Users Management'))

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">{{ __('Administration') }} /</span> {{ __('Users Management') }}
</h4>

@if (session('success'))
<div class="alert alert-success alert-dismissible" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ __('Users List') }}</h5>
    </div>
    <x-feature-section feature="admin.users.view-list" showDeniedMessage="true">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover border-top">
                <thead>
                    <tr>
                        <th>{{ __('User') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Roles') }}</th>
                        <th>{{ __('KYC Status') }}</th>
                        <th>{{ __('Registered') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2">
                                    <img src="{{ $user->profile_photo_url }}" alt="Avatar" class="rounded-circle">
                                </div>
                                <div>
                                    <strong>{{ $user->name }}</strong>
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->roles->count() > 0)
                                @foreach($user->roles as $role)
                                    <span class="badge me-1" style="background-color: #B8860B; color: white;">
                                        {{ $role->nom }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-muted">{{ __('No roles') }}</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusColors = [
                                    'verified' => 'success',
                                    'pending' => 'warning',
                                    'rejected' => 'danger',
                                ];
                                $color = $statusColors[$user->kyc_status ?? 'none'] ?? 'secondary';
                            @endphp
                            <span class="badge bg-label-{{ $color }}">
                                {{ $user->kyc_status ? __(ucfirst($user->kyc_status)) : __('None') }}
                            </span>
                        </td>
                        <td>{{ $user->created_at->format('d/m/Y') }}</td>
                        <td>
                            <x-feature-link feature="admin.users.manage-roles" route="{{ route('admin.users.roles', $user->id) }}" class="btn btn-sm btn-primary">
                                <i class="bx bx-user-check me-1"></i> {{ __('Manage Roles') }}
                            </x-feature-link>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            {{ __('No users found.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $users->links() }}
        </div>
    </div>
    </x-feature-section>
</div>

@endsection

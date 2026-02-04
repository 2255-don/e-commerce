@extends('layouts/layoutMaster')

@section('title', __('Edit Role'))

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">{{ __('Administration') }} / {{ __('Roles') }} /</span> {{ $role->nom }}
</h4>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ __('Edit Role') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <x-feature-section feature="admin.roles.form-fields">
                        <div class="mb-3">
                            <label for="nom" class="form-label">{{ __('Role Name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom', $role->nom) }}" required autofocus>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">{{ __('Slug') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $role->slug) }}" required>
                            <small class="text-muted">{{ __('Unique identifier for the role') }}</small>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('Description') }}</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $role->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </x-feature-section>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bx bx-save me-1"></i> {{ __('Update Role') }}
                        </button>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-label-secondary">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="card-title">{{ __('Role Statistics') }}</h6>
                <p class="mb-2">
                    <i class="bx bx-user me-1" style="color: #808080;"></i>
                    <strong>{{ $role->users_count }}</strong> {{ __('users assigned') }}
                </p>
                <p class="mb-0">
                    <i class="bx bx-shield me-1" style="color: #B8860B;"></i>
                    <strong>{{ $role->permissions_count }}</strong> {{ __('permissions') }}
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">{{ __('Quick Actions') }}</h6>
                <a href="{{ route('admin.roles.permissions', $role->id) }}" class="btn btn-sm btn-outline-primary w-100 mb-2">
                    <i class="bx bx-shield me-1"></i> {{ __('Manage Permissions') }}
                </a>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-outline-secondary w-100">
                    <i class="bx bx-arrow-back me-1"></i> {{ __('Back to Roles') }}
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

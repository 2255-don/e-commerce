@extends('layouts/layoutMaster')

@section('title', __('Create Role'))

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">{{ __('Administration') }} / {{ __('Roles') }} /</span> {{ __('Create') }}
</h4>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ __('Create New Role') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.roles.store') }}" method="POST">
                    @csrf

                    <x-feature-section feature="admin.roles.form-fields">
                        <div class="mb-3">
                            <label for="nom" class="form-label">{{ __('Role Name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom') }}" required autofocus>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">{{ __('Slug') }}</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug') }}" placeholder="{{ __('Leave empty to auto-generate') }}">
                            <small class="text-muted">{{ __('Unique identifier for the role (auto-generated if empty)') }}</small>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('Description') }}</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </x-feature-section>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bx bx-save me-1"></i> {{ __('Create Role') }}
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
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">{{ __('Help') }}</h6>
                <p class="text-muted small">
                    <strong>{{ __('Role Name') }}:</strong><br>
                    {{ __('The display name of the role (e.g., "Manager", "Editor")') }}
                </p>
                <p class="text-muted small">
                    <strong>{{ __('Slug') }}:</strong><br>
                    {{ __('A unique identifier used in code. If empty, it will be auto-generated from the role name.') }}
                </p>
                <p class="text-muted small mb-0">
                    <strong>{{ __('Next Steps') }}:</strong><br>
                    {{ __('After creating the role, assign permissions to it via "Manage Permissions".') }}
                </p>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-script')
<script>
$(document).ready(function() {
    // Auto-generate slug from name
    $('#nom').on('input', function() {
        const name = $(this).val();
        const slug = name.toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        $('#slug').attr('placeholder', slug || '{{ __("Leave empty to auto-generate") }}');
    });
});
</script>
@endsection

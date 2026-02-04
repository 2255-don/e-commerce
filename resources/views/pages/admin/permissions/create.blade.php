@extends('layouts/layoutMaster')

@section('title', __('Create Permission'))

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">{{ __('Administration') }} / {{ __('Permissions') }} /</span> {{ __('Create') }}
</h4>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">{{ __('Permission Information') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.permissions.store') }}" method="POST">
                    @csrf
                    
                    <x-feature-section feature="admin.permissions.form-basic-info">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="name">{{ __('Name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="slug">{{ __('Slug') }}</label>
                                <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug') }}">
                                <small class="text-muted">{{ __('Leave empty to auto-generate from name') }}</small>
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="description">{{ __('Description') }}</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </x-feature-section>

                    <x-feature-section feature="admin.permissions.form-features">

                    <div class="mb-3">
                        <label class="form-label">{{ __('Associated Features') }}</label>
                        <div class="card">
                            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                @foreach($features->groupBy('module.name') as $moduleName => $moduleFeatures)
                                    <h6 class="mt-3 mb-2" style="color: #B8860B;">
                                        <i class="bx {{ $moduleFeatures->first()->module->icon ?? 'bx-cube' }} me-1"></i>
                                        {{ $moduleName }}
                                    </h6>
                                    <div class="row">
                                        @foreach($moduleFeatures as $feature)
                                            <div class="col-md-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="features[]" value="{{ $feature->id }}" id="feature-{{ $feature->id }}" {{ in_array($feature->id, old('features', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="feature-{{ $feature->id }}">
                                                        {{ $feature->name }}
                                                        <small class="text-muted">({{ $feature->slug }})</small>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    </x-feature-section>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bx bx-save me-1"></i> {{ __('Create Permission') }}
                        </button>
                        <a href="{{ route('admin.permissions.index') }}" class="btn btn-label-secondary">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-script')
<script>
$(document).ready(function() {
    // Auto-generate slug from name
    $('#name').on('input', function() {
        const name = $(this).val();
        const slug = name.toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        $('#slug').val(slug);
    });
});
</script>
@endsection

@extends('layouts/layoutMaster')

@section('title', __('Edit Module'))

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">{{ __('Administration') }} / {{ __('Modules') }} /</span> {{ __('Edit') }}
</h4>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">{{ __('Module Information') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.modules.update', $module->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <x-feature-section feature="admin.modules.form-basic-info">
                        <div class="mb-3">
                            <label class="form-label" for="name">{{ __('Name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $module->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="slug">{{ __('Slug') }}</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $module->slug) }}">
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </x-feature-section>

                    <x-feature-section feature="admin.modules.form-appearance">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="icon">{{ __('Icon') }} (Boxicons)</label>
                            <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" value="{{ old('icon', $module->icon) }}" placeholder="bx-cube">
                            <small class="text-muted"><a href="https://boxicons.com/" target="_blank">{{ __('Browse Boxicons') }}</a></small>
                            @error('icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="color">{{ __('Color') }}</label>
                            <input type="color" class="form-control @error('color') is-invalid @enderror" id="color" name="color" value="{{ old('color', $module->color) }}">
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    </x-feature-section>

                    <x-feature-section feature="admin.modules.form-settings">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="order">{{ __('Order') }}</label>
                                <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', $module->order) }}">
                                @error('order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Type') }}</label>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="is_core" name="is_core" value="1" {{ old('is_core', $module->is_core) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_core">
                                        {{ __('Core Module') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </x-feature-section>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bx bx-save me-1"></i> {{ __('Update Module') }}
                        </button>
                        <a href="{{ route('admin.modules.index') }}" class="btn btn-label-secondary">
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
                <h6 class="card-title">{{ __('Module Preview') }}</h6>
                <div class="d-flex align-items-center mt-3 p-3 bg-light rounded">
                    <i class='bx {{ $module->icon }} bx-lg me-3' id="preview-icon" style="color: {{ $module->color }}"></i>
                    <div>
                        <strong id="preview-name">{{ $module->name }}</strong><br>
                        <small class="text-muted" id="preview-slug">{{ $module->slug }}</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">{{ __('Statistics') }}</h6>
                <p class="text-muted mb-2">
                    <i class="bx bx-grid-alt me-1"></i> 
                    <strong>{{ $module->features->count() }}</strong> {{ __('features') }}
                </p>
                <p class="text-muted mb-0">
                    <i class="bx bx-calendar me-1"></i> 
                    {{ __('Created') }}: {{ $module->created_at->format('d/m/Y') }}
                </p>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-script')
<script>
$(document).ready(function() {
    // Update slug from name
    $('#name').on('input', function() {
        const name = $(this).val();
        $('#preview-name').text(name || '{{ $module->name }}');
    });

    // Update icon preview
    $('#icon').on('input', function() {
        const icon = $(this).val() || '{{ $module->icon }}';
        $('#preview-icon').attr('class', 'bx ' + icon + ' bx-lg me-3');
    });

    // Update color preview
    $('#color').on('input', function() {
        const color = $(this).val() || '{{ $module->color }}';
        $('#preview-icon').css('color', color);
    });
});
</script>
@endsection

@extends('layouts/layoutMaster')

@section('title', __('Create') . ' ' . __('Profile'))

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">{{ __('Administration') }} / {{ __('Profile Management') }} /</span> {{ __('Create') }}
</h4>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ __('New Profile') }}</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <form action="{{ route('admin.profils.store') }}" method="POST">
                    @csrf
                    
                    <x-feature-section feature="admin.profils.form-fields">
                        <div class="mb-3">
                            <label for="libelle" class="form-label">{{ __('Label of the profile') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="libelle" name="libelle" 
                                   value="{{ old('libelle') }}" required 
                                   placeholder="{{ __('Examples: Super-Admin, Support Agent, KYC Verifier, user, Enterprise Admin') }}">
                            <small class="form-text text-muted">
                                {{ __('Examples: Super-Admin, Support Agent, KYC Verifier, user, Enterprise Admin') }}
                            </small>
                        </div>
                    </x-feature-section>
                    
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="ti ti-device-floppy me-1"></i> {{ __('Save') }}
                        </button>
                        <a href="{{ route('admin.profils.index') }}" class="btn btn-label-secondary">
                            <i class="ti ti-arrow-left me-1"></i> {{ __('Back') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

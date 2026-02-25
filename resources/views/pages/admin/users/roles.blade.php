@extends('layouts/layoutMaster')

@section('title', __('User Roles'))

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">{{ __('Administration') }} / {{ __('Users') }} /</span> {{ $user->name }} -
        {{ __('Roles') }}
    </h4>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Assign Roles') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.roles.update', $user->id) }}" method="POST">
                        @csrf

                        <x-feature-section feature="admin.users.assign-roles">
                            <div class="mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="select-all-roles">
                                    <label class="form-check-label fw-bold" for="select-all-roles">
                                        {{ __('Select All Roles') }}
                                    </label>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-body">
                                    @foreach ($allRoles as $role)
                                        <div class="mb-3 pb-3 border-bottom">
                                            <div class="form-check">
                                                <input class="form-check-input role-checkbox" type="checkbox" name="roles[]"
                                                    value="{{ $role->id }}" id="role-{{ $role->id }}"
                                                    {{ $user->roles->contains($role->id) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold" for="role-{{ $role->id }}">
                                                    {{ $role->nom }}
                                                    <span class="badge ms-2"
                                                        style="background-color: #808080; color: white;">
                                                        {{ $role->permissions_count ?? 0 }} {{ __('permissions') }}
                                                    </span>
                                                </label>
                                            </div>
                                            @if ($role->description)
                                                <small
                                                    class="text-muted d-block mt-1 ms-4">{{ $role->description }}</small>
                                            @endif
                                            <small class="text-muted d-block ms-4">
                                                <code>{{ $role->slug }}</code>
                                            </small>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </x-feature-section>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bx bx-save me-1"></i> {{ __('Update Roles') }}
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-label-secondary">
                                {{ __('Back to Users') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="card-title">{{ __('User Information') }}</h6>
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-lg me-3">
                            <img src="{{ $user->profile_photo_url }}" alt="Avatar" class="rounded-circle">
                        </div>
                        <div>
                            <strong>{{ $user->name }}</strong><br>
                            <small class="text-muted">{{ $user->email }}</small>
                        </div>
                    </div>
                    <p class="mb-2">
                        <strong>{{ __('KYC') }}:</strong>
                        <span class="badge bg-label-{{ $user->kyc_status === 'verified' ? 'success' : 'warning' }}">
                            {{ $user->kyc_status ? __(ucfirst($user->kyc_status)) : __('None') }}
                        </span>
                    </p>
                    <p class="mb-0">
                        <strong>{{ __('Member since') }}:</strong><br>
                        <small class="text-muted">{{ $user->created_at->format('d/m/Y') }}</small>
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">{{ __('Current Statistics') }}</h6>
                    <p class="text-muted mb-2">
                        <i class="bx bx-user-check me-1" style="color: #B8860B;"></i>
                        <strong id="selected-count">{{ $user->roles->count() }}</strong> {{ __('roles assigned') }}
                    </p>
                    @if ($user->sellerProfile)
                        <p class="text-muted mb-0">
                            <i class="bx bx-store me-1" style="color: #808080;"></i>
                            {{ __('Active Seller') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            // Select/Deselect All
            $('#select-all-roles').on('change', function() {
                $('.role-checkbox').prop('checked', $(this).prop('checked'));
                updateCount();
            });

            // Update count when individual checkbox changes
            $('.role-checkbox').on('change', function() {
                updateCount();

                // Update "select all" state
                const total = $('.role-checkbox').length;
                const checked = $('.role-checkbox:checked').length;
                $('#select-all-roles').prop('checked', total === checked);
            });

            function updateCount() {
                const count = $('.role-checkbox:checked').length;
                $('#selected-count').text(count);
            }
        });
    </script>
@endsection

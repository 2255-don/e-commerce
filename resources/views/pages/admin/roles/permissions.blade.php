@extends('layouts/layoutMaster')

@section('title', __('Role Permissions'))

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">{{ __('Administration') }} / {{ __('Roles') }} /</span> {{ $role->nom }} -
        {{ __('Permissions') }}
    </h4>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Assign Permissions') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.roles.permissions.update', $role->id) }}" method="POST">
                        @csrf

                        <x-feature-section feature="admin.roles.assign-permissions">
                            <div class="mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="select-all">
                                    <label class="form-check-label fw-bold" for="select-all">
                                        {{ __('Select All Permissions') }}
                                    </label>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                                    @foreach ($allPermissions as $permission)
                                        <div class="mb-3 pb-3 border-bottom">
                                            <div class="form-check">
                                                <input class="form-check-input permission-checkbox" type="checkbox"
                                                    name="permissions[]" value="{{ $permission->id }}"
                                                    id="permission-{{ $permission->id }}"
                                                    {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold"
                                                    for="permission-{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                    <span class="badge ms-2"
                                                        style="background-color: #B8860B; color: white;">
                                                        {{ $permission->features_count ?? 0 }} {{ __('features') }}
                                                    </span>
                                                </label>
                                            </div>
                                            @if ($permission->description)
                                                <small
                                                    class="text-muted d-block mt-1 ms-4">{{ $permission->description }}</small>
                                            @endif
                                            <small class="text-muted d-block ms-4">
                                                <code>{{ $permission->slug }}</code>
                                            </small>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </x-feature-section>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bx bx-save me-1"></i> {{ __('Update Permissions') }}
                            </button>
                            <a href="{{ route('admin.profils.index') }}" class="btn btn-label-secondary">
                                {{ __('Back to Roles') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="card-title">{{ __('Role Information') }}</h6>
                    <p class="mb-2">
                        <strong>{{ __('Name') }}:</strong> {{ $role->nom }}
                    </p>
                    <p class="mb-2">
                        <strong>{{ __('Slug') }}:</strong> <code>{{ $role->slug }}</code>
                    </p>
                    @if ($role->description)
                        <p class="mb-0">
                            <strong>{{ __('Description') }}:</strong><br>
                            <small class="text-muted">{{ $role->description }}</small>
                        </p>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">{{ __('Current Statistics') }}</h6>
                    <p class="text-muted mb-2">
                        <i class="bx bx-shield me-1" style="color: #B8860B;"></i>
                        <strong id="selected-count">{{ $role->permissions->count() }}</strong>
                        {{ __('permissions assigned') }}
                    </p>
                    <p class="text-muted mb-0">
                        <i class="bx bx-user me-1" style="color: #808080;"></i>
                        <strong>{{ $role->users->count() }}</strong> {{ __('users with this role') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            // Select/Deselect All
            $('#select-all').on('change', function() {
                $('.permission-checkbox').prop('checked', $(this).prop('checked'));
                updateCount();
            });

            // Update count when individual checkbox changes
            $('.permission-checkbox').on('change', function() {
                updateCount();

                // Update "select all" state
                const total = $('.permission-checkbox').length;
                const checked = $('.permission-checkbox:checked').length;
                $('#select-all').prop('checked', total === checked);
            });

            function updateCount() {
                const count = $('.permission-checkbox:checked').length;
                $('#selected-count').text(count);
            }
        });
    </script>
@endsection

@extends('layouts/layoutMaster')

@section('title', __('Roles Management'))

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">{{ __('Administration') }} /</span> {{ __('Roles') }}
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ __('Roles List') }}</h5>
            <x-feature-link feature="admin.roles.create" route="{{ route('admin.roles.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> {{ __('Add New Role') }}
            </x-feature-link>
        </div>
        <x-feature-section feature="admin.roles.view-list" showDeniedMessage="true">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover border-top">
                        <thead>
                            <tr>
                                <th>{{ __('Role Name') }}</th>
                                <th>{{ __('Slug') }}</th>
                                <th>{{ __('Description') }}</th>
                                <th>{{ __('Users') }}</th>
                                <th>{{ __('Permissions') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($roles as $role)
                                <tr>
                                    <td>
                                        <strong>{{ $role->nom }}</strong>
                                    </td>
                                    <td><code>{{ $role->slug }}</code></td>
                                    <td>
                                        <span class="text-muted">{{ $role->description ?? __('No description') }}</span>
                                    </td>
                                    <td>
                                        <span class="badge" style="background-color: #808080; color: white;">
                                            {{ $role->users_count }} {{ __('users') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge" style="background-color: #B8860B; color: white;">
                                            {{ $role->permissions_count }} {{ __('permissions') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown">
                                                <i class="ti ti-dots-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.roles.permissions', $role->id) }}">
                                                    <i class="bx bx-shield me-1"></i> {{ __('Manage Permissions') }}
                                                </a>
                                                <a class="dropdown-item" href="{{ route('admin.roles.edit', $role->id) }}">
                                                    <i class="ti ti-pencil me-1"></i> {{ __('Edit') }}
                                                </a>
                                                <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST"
                                                    class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="dropdown-item text-danger delete-btn">
                                                        <i class="ti ti-trash me-1"></i> {{ __('Delete') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        {{ __('No roles found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </x-feature-section>
    </div>

@endsection

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            // Delete confirmation
            $('.delete-btn').on('click', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');

                Swal.fire({
                    title: '{{ __('Are you sure?') }}',
                    text: "{{ __('This action cannot be undone!') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '{{ __('Yes, delete it!') }}',
                    cancelButtonText: '{{ __('Cancel') }}',
                    customClass: {
                        confirmButton: 'btn btn-danger me-3',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection

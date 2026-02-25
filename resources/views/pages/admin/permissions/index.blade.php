@extends('layouts/layoutMaster')

@section('title', __('Permissions Management'))

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">{{ __('Administration') }} /</span> {{ __('Permissions Management') }}
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ __('Permissions List') }}</h5>
            <x-feature-link feature="admin.permissions.create" route="{{ route('admin.permissions.create') }}"
                class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> {{ __('Add Permission') }}
            </x-feature-link>
        </div>
        <x-feature-section feature="admin.permissions.view-list" showDeniedMessage="true">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover border-top">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Slug') }}</th>
                                <th>{{ __('Features') }}</th>
                                <th>{{ __('Description') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($permissions as $permission)
                                <tr>
                                    <td>
                                        <strong>{{ $permission->name }}</strong>
                                    </td>
                                    <td>
                                        <code class="text-muted">{{ $permission->slug }}</code>
                                    </td>
                                    <td>
                                        <span class="badge" style="background-color: #B8860B; color: white;">
                                            {{ $permission->features_count ?? 0 }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ Str::limit($permission->description, 60) }}</small>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <x-feature-link feature="admin.permissions.view"
                                                    route="{{ route('admin.permissions.show', $permission->id) }}"
                                                    class="dropdown-item">
                                                    <i class="bx bx-show me-1"></i> {{ __('View Details') }}
                                                </x-feature-link>
                                                <x-feature-link feature="admin.permissions.edit"
                                                    route="{{ route('admin.permissions.edit', $permission->id) }}"
                                                    class="dropdown-item">
                                                    <i class="bx bx-edit me-1"></i> {{ __('Edit') }}
                                                </x-feature-link>
                                                <form action="{{ route('admin.permissions.destroy', $permission->id) }}"
                                                    method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-feature-button feature="admin.permissions.delete" type="button"
                                                        class="dropdown-item text-danger delete-btn">
                                                        <i class="bx bx-trash me-1"></i> {{ __('Delete') }}
                                                    </x-feature-button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        {{ __('No permissions found.') }}
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

@section('page-script')
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.delete-btn').on('click', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');

                Swal.fire({
                    title: '{{ __('Are you sure?') }}',
                    text: "{{ __('This action is irreversible!') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '{{ __('Yes, delete!') }}',
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

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endsection

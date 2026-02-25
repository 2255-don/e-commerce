@extends('layouts/layoutMaster')

@section('title', __('Profile Management'))

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endsection



@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
@endsection

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">{{ __('Administration') }} /</span> {{ __('Profile Management') }}
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ __('Profile List') }}</h5>
            <x-feature-link feature="admin.profils.create" route="{{ route('admin.profils.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i> {{ __('Add New Profile') }}
            </x-feature-link>
        </div>
        <x-feature-section feature="admin.profils.view-list" showDeniedMessage="true">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover border-top">
                        <thead>
                            <tr>
                                <th>{{ __('Label') }}</th>
                                <th>{{ __('Number of users') }}</th>
                                <th>{{ __('Created on') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($profils as $profil)
                                <tr>
                                    <td>
                                        <span class="badge bg-label-primary">{{ $profil->libelle }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-info">{{ $profil->users_count }}
                                            {{ __('user(s)') }}</span>
                                    </td>
                                    <td>{{ $profil->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown">
                                                <i class="ti ti-dots-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <x-feature-link feature="profils.edit"
                                                    route="{{ route('admin.profils.edit', $profil->id) }}"
                                                    class="dropdown-item">
                                                    <i class="ti ti-pencil me-1"></i> {{ __('Edit') }}
                                                </x-feature-link>
                                                <x-feature-link feature="admin.profils.manage-permissions"
                                                    route="{{ route('admin.roles.permissions', $profil->id) }}"
                                                    class="dropdown-item">
                                                    <i class="bx bx-shield me-1"></i> {{ __('Manage Permissions') }}
                                                </x-feature-link>
                                                <form action="{{ route('admin.profils.destroy', $profil->id) }}"
                                                    method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-feature-button feature="profils.delete" type="button"
                                                        class="dropdown-item text-danger delete-btn">
                                                        <i class="ti ti-trash me-1"></i> {{ __('Delete') }}
                                                    </x-feature-button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </x-feature-section>
    </div>

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

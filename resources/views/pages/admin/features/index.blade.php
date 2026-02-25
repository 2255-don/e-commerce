@extends('layouts/layoutMaster')

@section('title', __('Features Management'))

@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">{{ __('Administration') }} /</span> {{ __('Features Management') }}
    </h4>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">{{ __('Features List') }}</h5>
                <x-feature-link feature="admin.features.create" route="{{ route('admin.features.create') }}"
                    class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i> {{ __('Add Feature') }}
                </x-feature-link>
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <select class="form-select" id="module-filter">
                        <option value="">{{ __('All Modules') }}</option>
                        @foreach ($modules as $module)
                            <option value="{{ $module->id }}" {{ request('module') == $module->id ? 'selected' : '' }}>
                                {{ $module->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select class="form-select" id="type-filter">
                        <option value="">{{ __('All Types') }}</option>
                        <option value="route" {{ request('type') == 'route' ? 'selected' : '' }}>{{ __('Route') }}
                        </option>
                        <option value="action" {{ request('type') == 'action' ? 'selected' : '' }}>{{ __('Action') }}
                        </option>
                        <option value="ui_element" {{ request('type') == 'ui_element' ? 'selected' : '' }}>
                            {{ __('UI Element') }}</option>
                        <option value="section" {{ request('type') == 'section' ? 'selected' : '' }}>{{ __('Section') }}
                        </option>
                    </select>
                </div>
            </div>
        </div>
        <x-feature-section feature="admin.features.view-list" showDeniedMessage="true">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover border-top">
                        <thead>
                            <tr>
                                <th>{{ __('Module') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Slug') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($features as $feature)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class='bx {{ $feature->module->icon ?? 'bx-cube' }} me-2'
                                                style="color: {{ $feature->module->color ?? '#B8860B' }}"></i>
                                            <span>{{ $feature->module->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ $feature->name }}</strong>
                                        @if ($feature->description)
                                            <br><small
                                                class="text-muted">{{ Str::limit($feature->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <code class="text-muted">{{ $feature->slug }}</code>
                                    </td>
                                    <td>
                                        @php
                                            $typeColors = [
                                                'route' => 'primary',
                                                'action' => 'success',
                                                'ui_element' => 'info',
                                                'section' => 'warning',
                                            ];
                                            $color = $typeColors[$feature->type] ?? 'secondary';
                                        @endphp
                                        <span
                                            class="badge bg-label-{{ $color }}">{{ __(ucfirst(str_replace('_', ' ', $feature->type))) }}</span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <x-feature-link feature="admin.features.edit"
                                                    route="{{ route('admin.features.edit', $feature->id) }}"
                                                    class="dropdown-item">
                                                    <i class="bx bx-edit me-1"></i> {{ __('Edit') }}
                                                </x-feature-link>
                                                <form action="{{ route('admin.features.destroy', $feature->id) }}"
                                                    method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-feature-button feature="admin.features.delete" type="button"
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
                                        {{ __('No features found.') }}
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
            // Filter handling
            $('#module-filter, #type-filter').on('change', function() {
                const module = $('#module-filter').val();
                const type = $('#type-filter').val();

                let url = '{{ route('admin.features.index') }}';
                const params = [];

                if (module) params.push('module=' + module);
                if (type) params.push('type=' + type);

                if (params.length > 0) {
                    url += '?' + params.join('&');
                }

                window.location.href = url;
            });

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

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endsection

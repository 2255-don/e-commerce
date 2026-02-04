@extends('layouts/layoutMaster')

@section('title', __('Modules Management'))

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">{{ __('Administration') }} /</span> {{ __('Modules Management') }}
</h4>

@if (session('success'))
<div class="alert alert-success alert-dismissible" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if (session('error'))
<div class="alert alert-danger alert-dismissible" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ __('Modules List') }}</h5>
        <x-feature-link feature="admin.modules.create" route="{{ route('admin.modules.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> {{ __('Add Module') }}
        </x-feature-link>
    </div>
    <x-feature-section feature="admin.modules.view-list" showDeniedMessage="true">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover border-top">
                    <thead>
                        <tr>
                            <th>{{ __('Icon') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Slug') }}</th>
                            <th>{{ __('Features Count') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Order') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($modules as $module)
                        <tr>
                            <td>
                                <i class='bx {{ $module->icon ?? "bx-cube" }} bx-sm' style="color: {{ $module->color ?? '#B8860B' }}"></i>
                            </td>
                            <td>
                                <strong>{{ $module->name }}</strong>
                            </td>
                            <td>
                                <code class="text-muted">{{ $module->slug }}</code>
                            </td>
                            <td>
                                <span class="badge bg-label-info">{{ $module->features_count ?? 0 }}</span>
                            </td>
                            <td>
                                @if($module->is_core)
                                    <span class="badge bg-label-warning">{{ __('Core') }}</span>
                                @else
                                    <span class="badge bg-label-secondary">{{ __('Custom') }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-label-primary">{{ $module->order }}</span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <x-feature-link feature="admin.modules.edit" route="{{ route('admin.modules.edit', $module->id) }}" class="dropdown-item">
                                            <i class="bx bx-edit me-1"></i> {{ __('Edit') }}
                                        </x-feature-link>
                                        @if(!$module->is_core)
                                        <form action="{{ route('admin.modules.destroy', $module->id) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <x-feature-button feature="admin.modules.delete" type="button" class="dropdown-item text-danger delete-btn">
                                                <i class="bx bx-trash me-1"></i> {{ __('Delete') }}
                                            </x-feature-button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                {{ __('No modules found.') }}
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
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
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
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endsection

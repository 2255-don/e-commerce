@props([
    'feature',
    'showDeniedMessage' => false,
])

@php
    $user = auth()->user();
    $service = app(\App\Services\FeatureAccessService::class);
    $canAccess = $service->canAccess($user, $feature);
@endphp

@if ($canAccess)
    {{ $slot }}
@else
    @if ($showDeniedMessage)
        <div class="alert alert-warning" role="alert">
            <i class="bx bx-error-circle me-2"></i>
            {{ __('You do not have permission to view this section.') }}
        </div>
    @endif
@endif

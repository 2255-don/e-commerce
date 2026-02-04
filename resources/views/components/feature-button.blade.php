@props([
    'feature',
    'hideIfDenied' => true,
])

@php
    $user = auth()->user();
    $service = app(\App\Services\FeatureAccessService::class);
    $canAccess = $service->canAccess($user, $feature);
@endphp

@if ($canAccess)
    <button {{ $attributes }}>
        {{ $slot }}
    </button>
@else
    @if (!$hideIfDenied)
        <button {{ $attributes->merge(['disabled' => true, 'class' => 'disabled']) }}>
            {{ $slot }}
        </button>
    @endif
@endif

@props([
    'feature',
    'route',
    'hideIfDenied' => true,
])

@php
    $user = auth()->user();
    $service = app(\App\Services\FeatureAccessService::class);
    $canAccess = $service->canAccess($user, $feature);
@endphp

@if ($canAccess)
    <a href="{{ $route }}" {{ $attributes }}>
        {{ $slot }}
    </a>
@else
    @if (!$hideIfDenied)
        <a {{ $attributes->merge(['class' => 'disabled text-muted', 'style' => 'pointer-events: none;']) }}>
            {{ $slot }}
        </a>
    @endif
@endif

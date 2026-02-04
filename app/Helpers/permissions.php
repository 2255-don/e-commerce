<?php

use App\Services\FeatureAccessService;

if (!function_exists('can_access_feature')) {
    /**
     * Check if the current user can access a feature
     *
     * @param string $featureSlug
     * @return bool
     */
    function can_access_feature(string $featureSlug): bool
    {
        $service = app(FeatureAccessService::class);
        return $service->canAccess(auth()->user(), $featureSlug);
    }
}

if (!function_exists('feature_auth_message')) {
    /**
     * Get authorization message for a feature
     *
     * @param string $featureSlug
     * @return string
     */
    function feature_auth_message(string $featureSlug): string
    {
        $service = app(FeatureAccessService::class);
        return $service->getAuthMessage($featureSlug);
    }
}

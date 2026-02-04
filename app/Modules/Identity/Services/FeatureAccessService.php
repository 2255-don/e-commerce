<?php

namespace Modules\Identity\Services;

use App\Models\Feature;

class FeatureAccessService
{
    /**
     * Check if user can access a feature
     * 
     * Logic (Permissive Mode):
     * 1. Feature not linked to any permission → ALLOW
     * 2. Permissions not linked to any role → ALLOW
     * 3. User has at least one required role → ALLOW
     * 4. Otherwise → DENY
     */
    public function canAccess($user, string $featureSlug): bool
    {
        // No user → Deny
        if (!$user) {
            return false;
        }

        // Find the feature
        $feature = Feature::where('slug', $featureSlug)
            ->with(['permissions.roles'])
            ->first();

        // Feature doesn't exist → Allow (permissive)
        if (!$feature) {
            return true;
        }

        // Feature has no permissions → Allow (permissive)
        if ($feature->permissions->isEmpty()) {
            return true;
        }

        // Check if any permission is linked to roles
        $permissionsWithRoles = $feature->permissions->filter(function ($permission) {
            return $permission->roles->isNotEmpty();
        });

        // No permissions are linked to roles → Allow (permissive)
        if ($permissionsWithRoles->isEmpty()) {
            return true;
        }

        // Permissions are linked to roles → Check if user has any of those roles
        $requiredRoleIds = $permissionsWithRoles
            ->flatMap(fn($permission) => $permission->roles->pluck('id'))
            ->unique()
            ->values();

        $userRoleIds = $user->roles->pluck('id');

        return $userRoleIds->intersect($requiredRoleIds)->isNotEmpty();
    }

    /**
     * Get authorization message for a feature
     */
    public function getAuthMessage(string $featureSlug): string
    {
        $feature = Feature::where('slug', $featureSlug)->first();
        
        if (!$feature) {
            return __('This feature is not configured yet');
        }

        return __('You do not have permission to access this feature');
    }
}

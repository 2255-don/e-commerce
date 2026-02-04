<?php

namespace App\Services;

use App\Models\Feature;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Str;

class PermissionService
{
    /**
     * Get all permissions with feature count
     */
    public function getAllPermissions()
    {
        return Permission::withCount('features')->get();
    }

    /**
     * Get a single permission with related data
     */
    public function getPermission(string $id)
    {
        return Permission::with(['features.module', 'roles'])->findOrFail($id);
    }

    /**
     * Create a new permission
     */
    public function createPermission(array $data)
    {
        // Auto-generate slug if not provided
        if (!isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $permission = Permission::create($data);

        // Attach features if provided
        if (isset($data['features'])) {
            $permission->features()->sync($data['features']);
        }

        return $permission;
    }

    /**
     * Update an existing permission
     */
    public function updatePermission(string $id, array $data)
    {
        $permission = Permission::findOrFail($id);
        
        // Update basic fields
        $permission->update([
            'slug' => $data['slug'] ?? $permission->slug,
            'name' => $data['name'] ?? $permission->name,
            'description' => $data['description'] ?? $permission->description,
        ]);

        // Update features if provided
        if (isset($data['features'])) {
            $permission->features()->sync($data['features']);
        }

        return $permission;
    }

    /**
     * Delete a permission
     */
    public function deletePermission(string $id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();
    }

    /**
     * Assign permission to a role
     */
    public function assignToRole(string $permissionId, string $roleId)
    {
        $permission = Permission::findOrFail($permissionId);
        $role = Role::findOrFail($roleId);
        
        $role->permissions()->syncWithoutDetaching([$permissionId]);
    }

    /**
     * Remove permission from a role
     */
    public function removeFromRole(string $permissionId, string $roleId)
    {
        $permission = Permission::findOrFail($permissionId);
        $role = Role::findOrFail($roleId);
        
        $role->permissions()->detach($permissionId);
    }

    /**
     * Sync permissions for a role
     */
    public function syncRolePermissions(string $roleId, array $permissionIds)
    {
        $role = Role::findOrFail($roleId);
        $role->permissions()->sync($permissionIds);
    }

    /**
     * Get permissions grouped by module
     */
    public function getPermissionsGroupedByModule()
    {
        return Permission::with(['features.module'])
            ->get()
            ->groupBy(function ($permission) {
                return $permission->features->first()->module->name ?? 'Uncategorized';
            });
    }

    /**
     * Generate default permissions for a module
     */
    public function generateDefaultPermissionsForModule(string $moduleId): array
    {
        $module = \App\Models\Module::findOrFail($moduleId);
        $features = Feature::where('module_id', $moduleId)->get();

        $permissions = [];
        $actions = ['view', 'create', 'edit', 'delete'];

        foreach ($actions as $action) {
            $slug = "{$module->slug}.{$action}";
            $name = ucfirst($action) . ' ' . $module->name;

            $permission = Permission::updateOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'description' => "Permission to {$action} {$module->name}"]
            );

            // Attach relevant features
            $relevantFeatures = $features->filter(function ($feature) use ($action) {
                return str_contains($feature->slug, $action);
            });

            if ($relevantFeatures->isNotEmpty()) {
                $permission->features()->sync($relevantFeatures->pluck('id')->toArray());
            }

            $permissions[] = $permission;
        }

        return $permissions;
    }

    /**
     * Check if a user has a specific permission via their roles
     */
    public function userHasPermission($user, string $permissionSlug): bool
    {
        return $user->roles()
            ->whereHas('permissions', function ($q) use ($permissionSlug) {
                $q->where('slug', $permissionSlug);
            })
            ->exists();
    }

    /**
     * Check if a user can access a feature
     */
    public function userCanAccessFeature($user, string $featureSlug): bool
    {
        return $user->roles()
            ->whereHas('permissions.features', function ($q) use ($featureSlug) {
                $q->where('slug', $featureSlug);
            })
            ->exists();
    }
}

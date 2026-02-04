<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Show the form for managing role permissions
     */
    public function permissions(string $roleId)
    {
        $role = Role::with('permissions')->findOrFail($roleId);
        $allPermissions = $this->permissionService->getAllPermissions();
        
        return view('pages.admin.roles.permissions', compact('role', 'allPermissions'));
    }

    /**
     * Update permissions for a role
     */
    public function updatePermissions(Request $request, string $roleId)
    {
        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        try {
            $this->permissionService->syncRolePermissions(
                $roleId,
                $validated['permissions'] ?? []
            );
            
            return redirect()->back()
                ->with('success', __('Role permissions updated successfully.'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}

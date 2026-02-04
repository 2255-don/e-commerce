<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Display a listing of permissions
     */
    public function index()
    {
        try {
            $permissions = $this->permissionService->getAllPermissions();
            
            return response()->json([
                'success' => true,
                'data' => $permissions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created permission
     */
   public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:permissions,slug',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'features.*' => 'exists:features,id',
        ]);

        try {
            $permission = $this->permissionService->createPermission($validated);
            
            return response()->json([
                'success' => true,
                'message' => __('Permission created successfully.'),
                'data' => $permission
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified permission
     */
    public function show(string $id)
    {
        try {
            $permission = $this->permissionService->getPermission($id);
            
            return response()->json([
                'success' => true,
                'data' => $permission
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified permission
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:permissions,slug,' . $id,
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'features.*' => 'exists:features,id',
        ]);

        try {
            $permission = $this->permissionService->updatePermission($id, $validated);
            
            return response()->json([
                'success' => true,
                'message' => __('Permission updated successfully.'),
                'data' => $permission
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Remove the specified permission
     */
    public function destroy(string $id)
    {
        try {
            $this->permissionService->deletePermission($id);
            
            return response()->json([
                'success' => true,
                'message' => __('Permission deleted successfully.')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}

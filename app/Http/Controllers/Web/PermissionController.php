<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\FeatureService;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    protected $permissionService;
    protected $featureService;

    public function __construct(PermissionService $permissionService, FeatureService $featureService)
    {
        $this->permissionService = $permissionService;
        $this->featureService = $featureService;
    }

    /**
     * Display a listing of permissions
     */
    public function index()
    {
        $permissions = $this->permissionService->getAllPermissions();
        
        return view('pages.admin.permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new permission
     */
    public function create()
    {
        $features = $this->featureService->getAllFeatures();
        
        return view('pages.admin.permissions.create', compact('features'));
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
            $this->permissionService->createPermission($validated);
            
            return redirect()->route('admin.permissions.index')
                ->with('success', __('Permission created successfully.'));
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified permission
     */
    public function show(string $id)
    {
        $permission = $this->permissionService->getPermission($id);
        
        return view('pages.admin.permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing a permission
     */
    public function edit(string $id)
    {
        $permission = $this->permissionService->getPermission($id);
        $features = $this->featureService->getAllFeatures();
        
        return view('pages.admin.permissions.edit', compact('permission', 'features'));
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
            $this->permissionService->updatePermission($id, $validated);
            
            return redirect()->route('admin.permissions.index')
                ->with('success', __('Permission updated successfully.'));
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified permission
     */
    public function destroy(string $id)
    {
        try {
            $this->permissionService->deletePermission($id);
            
            return redirect()->route('admin.permissions.index')
                ->with('success', __('Permission deleted successfully.'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}

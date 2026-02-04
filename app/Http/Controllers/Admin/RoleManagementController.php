<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleManagementController extends Controller
{
    /**
     * Display a listing of roles
     */
    public function index()
    {
        $roles = Role::withCount(['users', 'permissions'])->get();
        return view('pages.admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role
     */
    public function create()
    {
        return view('pages.admin.roles.create');
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:roles,slug',
            'description' => 'nullable|string',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['nom']);
        }

        Role::create($validated);

        return redirect()->route('admin.roles.index')
            ->with('success', __('Role created successfully.'));
    }

    /**
     * Show the form for editing a role
     */
    public function edit(string $id)
    {
        $role = Role::withCount(['users', 'permissions'])->findOrFail($id);
        return view('pages.admin.roles.edit', compact('role'));
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:roles,slug,' . $id,
            'description' => 'nullable|string',
        ]);

        $role->update($validated);

        return redirect()->route('admin.roles.index')
            ->with('success', __('Role updated successfully.'));
    }

    /**
     * Remove the specified role
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);

        // Check if any users have this role
        if ($role->users()->count() > 0) {
            return redirect()->back()
                ->with('error', __('Cannot delete this role because users are assigned to it.'));
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', __('Role deleted successfully.'));
    }
}

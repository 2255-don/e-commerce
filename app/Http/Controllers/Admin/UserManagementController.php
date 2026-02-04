<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    /**
     * Display a listing of users with their roles
     */
    public function index()
    {
        $users = User::with('roles')->paginate(20);
        return view('pages.admin.users.index', compact('users'));
    }

    /**
     * Show the form for managing user roles
     */
    public function roles(string $userId)
    {
        $user = User::with('roles')->findOrFail($userId);
        $allRoles = Role::all();
        
        return view('pages.admin.users.roles', compact('user', 'allRoles'));
    }

    /**
     * Update user roles
     */
    public function updateRoles(Request $request, string $userId)
    {
        $validated = $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $user = User::findOrFail($userId);
        $user->roles()->sync($validated['roles'] ?? []);
        
        return redirect()->back()
            ->with('success', __('User roles updated successfully.'));
    }
}

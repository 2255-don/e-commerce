<?php

namespace App\Http\Controllers\Admin;

use Modules\Identity\Entities\Profil;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ProfilController extends Controller
{
    /**
     * Display a listing of the profils.
     */
    public function index()
    {
        $profils = Profil::withCount('users')->get();
        return view('pages.admin.profils.index', compact('profils'));
    }

    /**
     * Show the form for creating a new profil.
     */
    public function create()
    {
        return view('pages.admin.profils.create');
    }

    /**
     * Store a newly created profil in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'libelle' => 'required|string|max:255|unique:profils,libelle',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Profil::create([
            'libelle' => $request->libelle,
        ]);

        return redirect()->route('admin.profils.index')
            ->with('success', __('Profile created successfully.'));
    }

    /**
     * Show the form for editing the specified profil.
     */
    public function edit($id)
    {
        $profil = Profil::findOrFail($id);
        return view('pages.admin.profils.edit', compact('profil'));
    }

    /**
     * Update the specified profil in storage.
     */
    public function update(Request $request, $id)
    {
        $profil = Profil::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'libelle' => 'required|string|max:255|unique:profils,libelle,' . $id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $profil->update([
            'libelle' => $request->libelle,
        ]);

        return redirect()->route('admin.profils.index')
            ->with('success', __('Profile updated successfully.'));
    }

    /**
     * Remove the specified profil from storage.
     */
    public function destroy($id)
    {
        $profil = Profil::findOrFail($id);

        // Check if any users have this profil
        if ($profil->users()->count() > 0) {
            return redirect()->back()
                ->with('error', __('Cannot delete this profile because users are using it.'));
        }

        $profil->delete();

        return redirect()->route('admin.profils.index')
            ->with('success', __('Profile deleted successfully.'));
    }
}

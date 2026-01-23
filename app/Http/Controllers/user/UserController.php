<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Exception;

class UserController extends Controller
{
    /**
     * Show the user profile.
     */
    public function show()
    {
        try {
            return view('pages.user.profile', [
                'user' => Auth::user(),
            ]);
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'affichage du profil user : ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Update the user profile.
     */
    public function update(Request $request)
    {
        try {
            $user = Auth::user();

            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'phone_number' => ['nullable', 'string', 'max:20'],
                'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            ]);

            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone_number = $request->phone_number;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            return back()->with('status', 'profile-updated');
        } catch (Exception $e) {
            Log::error('Erreur lors de la mise à jour du profil : ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'data' => $request->except(['password', 'password_confirmation'])
            ]);
            return back()->withErrors(['error' => 'Impossible de mettre à jour vos informations actuellement.']);
        }
    }
}

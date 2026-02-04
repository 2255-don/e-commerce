<?php

namespace Shared\Middleware;

use App\Models\Feature;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class CheckFeatureAccess
{
    /**
     * Handle an incoming request.
     * 
     * Vérifie automatiquement si la route actuelle est protégée par le système de permissions
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Pas d'utilisateur connecté → Laisser passer (géré par auth middleware)
        if (!$user) {
            return $next($request);
        }

        // Récupérer le nom de la route actuelle
        $routeName = Route::currentRouteName();
        
        // Pas de nom de route → Laisser passer
        if (!$routeName) {
            return $next($request);
        }

        // Vérifier si l'utilisateur peut accéder à cette route
        if (!$this->canAccessRoute($user, $routeName)) {
            abort(403, __('You do not have permission to access this resource'));
        }

        return $next($request);
    }

    /**
     * Vérifie si l'utilisateur peut accéder à une route
     * 
     * Logique permissive :
     * 1. Feature n'existe pas pour cette route → AUTORISER
     * 2. Feature pas liée à une permission → AUTORISER
     * 3. Permission pas liée à un rôle → AUTORISER
     * 4. Utilisateur a le rôle requis → AUTORISER
     * 5. Sinon → REFUSER
     */
    protected function canAccessRoute($user, string $routeName): bool
    {
        // Chercher une feature correspondant au nom de la route
        $feature = Feature::where('slug', $routeName)
            ->where('type', 'route')
            ->with(['permissions.roles'])
            ->first();

        // Feature n'existe pas → Autoriser (permissif)
        if (!$feature) {
            return true;
        }

        // Feature n'a pas de permissions → Autoriser (permissif)
        if ($feature->permissions->isEmpty()) {
            return true;
        }

        // Vérifier si au moins une permission est liée à des rôles
        $permissionsWithRoles = $feature->permissions->filter(function ($permission) {
            return $permission->roles->isNotEmpty();
        });

        // Aucune permission n'est liée à des rôles → Autoriser (permissif)
        if ($permissionsWithRoles->isEmpty()) {
            return true;
        }

        // Récupérer tous les IDs de rôles requis
        $requiredRoleIds = $permissionsWithRoles
            ->flatMap(fn($permission) => $permission->roles->pluck('id'))
            ->unique()
            ->values();

        // Récupérer les IDs de rôles de l'utilisateur
        $userRoleIds = $user->roles->pluck('id');

        // Vérifier si l'utilisateur a au moins un des rôles requis
        return $userRoleIds->intersect($requiredRoleIds)->isNotEmpty();
    }
}

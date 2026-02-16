<?php

namespace App\Shared\Middleware;

use Modules\Identity\Entities\Feature;
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

        // 🔍 DEBUG: Log détaillé pour investigation
        \Illuminate\Support\Facades\Log::info("🔍 CheckFeatureAccess: START", [
            'route' => $routeName,
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_roles' => $user->roles->pluck('name')->toArray(),
        ]);

        // Vérifier si l'utilisateur peut accéder à cette route
        if (!$this->canAccessRoute($user, $routeName)) {
            \Illuminate\Support\Facades\Log::warning("❌ CheckFeatureAccess: ACCESS DENIED", [
                'route' => $routeName,
                'user_id' => $user->id,
            ]);
            abort(403, __('You do not have permission to access this resource'));
        }

        \Illuminate\Support\Facades\Log::info("✅ CheckFeatureAccess: ACCESS GRANTED for '$routeName'");

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
            \Illuminate\Support\Facades\Log::info("CheckFeatureAccess: Feature '$routeName' not found or not route type. Allowed.");
            return true;
        }

        // Feature n'a pas de permissions → Autoriser (permissif)
        if ($feature->permissions->isEmpty()) {
            \Illuminate\Support\Facades\Log::info("✅ CheckFeatureAccess: Feature '$routeName' found but has NO permissions. Access ALLOWED (permissive).", [
                'permissions_count' => $feature->permissions->count(),
                'feature_id' => $feature->id,
            ]);
            return true;
        }

        // 🔍 DEBUG: La feature a des permissions, analysons-les
        \Illuminate\Support\Facades\Log::info("🔍 CheckFeatureAccess: Feature '$routeName' has permissions", [
            'permissions_count' => $feature->permissions->count(),
            'permissions' => $feature->permissions->pluck('name')->toArray(),
        ]);

        // Vérifier si au moins une permission est liée à des rôles
        $permissionsWithRoles = $feature->permissions->filter(function ($permission) {
            return $permission->roles->isNotEmpty();
        });

        // Aucune permission n'est liée à des rôles → Autoriser (permissif)
        if ($permissionsWithRoles->isEmpty()) {
            \Illuminate\Support\Facades\Log::info("✅ CheckFeatureAccess: Feature '$routeName' has permissions but NONE linked to roles. Access ALLOWED (permissive).", [
                'total_permissions' => $feature->permissions->count(),
                'permissions_with_roles' => 0,
            ]);
            return true;
        }

        // 🔍 DEBUG: Des permissions sont liées à des rôles, vérifier l'accès utilisateur
        \Illuminate\Support\Facades\Log::info("🔍 CheckFeatureAccess: Checking role-based access", [
            'permissions_with_roles' => $permissionsWithRoles->count(),
        ]);

        // Récupérer tous les IDs de rôles requis
        $requiredRoleIds = $permissionsWithRoles
            ->flatMap(fn($permission) => $permission->roles->pluck('id'))
            ->unique()
            ->values();

        // Récupérer les IDs de rôles de l'utilisateur
        $userRoleIds = $user->roles->pluck('id');

        // Vérifier si l'utilisateur a au moins un des rôles requis
        $allowed = $userRoleIds->intersect($requiredRoleIds)->isNotEmpty();
        
        if (!$allowed) {
            \Illuminate\Support\Facades\Log::warning("❌ CheckFeatureAccess: Access DENIED for '$routeName'", [
                'user_role_ids' => $userRoleIds->toArray(),
                'required_role_ids' => $requiredRoleIds->toArray(),
                'intersection' => $userRoleIds->intersect($requiredRoleIds)->toArray(),
            ]);
        } else {
            \Illuminate\Support\Facades\Log::info("✅ CheckFeatureAccess: Access GRANTED for '$routeName' (user has required role)");
        }

        return $allowed;
    }
}

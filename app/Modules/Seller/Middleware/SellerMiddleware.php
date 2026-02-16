<?php

namespace App\Modules\Seller\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SellerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Check if user has seller role/profile
        // Assuming user->sellerProfile exists and check status
        \Illuminate\Support\Facades\Log::info('🔍 SellerMiddleware: Checking access', [
            'user_id' => $user->id,
            'has_sellerProfile' => !is_null($user->sellerProfile),
            'sellerProfile_id' => $user->sellerProfile?->id,
            'kyc_status' => $user->kyc_status ?? 'null',
        ]);
        
        if (!$user->sellerProfile) {
            \Illuminate\Support\Facades\Log::warning('❌ SellerMiddleware: Access denied - No seller profile for User ID ' . $user->id);
            return redirect()->route('dashboard')->with('error', 'Vous devez avoir un compte vendeur pour accéder à cette section.');
        }
        
        \Illuminate\Support\Facades\Log::info('✅ SellerMiddleware: User has sellerProfile', [
            'shop_name' => $user->sellerProfile->shop_name,
            'license_active' => method_exists($user->sellerProfile, 'isLicenseActive') ? $user->sellerProfile->isLicenseActive() : 'method_not_found',
        ]);
        
        // Allow access to license page to purchase/renew
        if ($request->routeIs('seller.license') || $request->routeIs('seller.license.wallet')) {
            return $next($request);
        }

        if (!$user->sellerProfile->isLicenseActive()) {
             \Illuminate\Support\Facades\Log::info('❌ SellerMiddleware: Redirecting to dashboard - License inactive for User ID ' . $user->id);
             return redirect()->route('dashboard')->with('error', 'Votre licence vendeur a expiré.');
        }

        \Illuminate\Support\Facades\Log::info('✅ SellerMiddleware: Access GRANTED');
        return $next($request);
    }
}

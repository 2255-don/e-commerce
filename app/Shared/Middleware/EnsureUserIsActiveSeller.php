<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActiveSeller
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // 1. Check if user is logged in
        if (!$user) {
            return redirect()->route('login');
        }

        // 2. Check if has seller profile & license active & KYC verified
        if (
            !$user->sellerProfile || 
            !$user->sellerProfile->isLicenseActive() || 
            $user->kyc_status !== 'verified'
        ) {
            // Redirect to license page with error message or simply deny access
            return redirect()->route('seller.license')->withErrors(['error' => 'Accès restreint aux vendeurs vérifiés avec une licence active.']);
        }

        return $next($request);
    }
}

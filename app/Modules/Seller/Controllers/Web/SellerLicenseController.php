<?php

namespace Modules\Seller\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Fintech\ValueObjects\Amount;

class SellerLicenseController extends Controller
{
    public function show()
    {
        try {
            $user = Auth::user();
            $wallet = $user->wallet;

            return view('seller::license', compact('wallet'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SellerLicenseController@show error: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Erreur lors de l\'affichage de la licence: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'shop_name' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048', // Max 2MB
            'kyc_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // Max 5MB
        ]);

        try {
            $user = Auth::user();
            $wallet = $user->wallet;
            $amount = new Amount(5000);

            // 0. Check Status
            if ($user->sellerProfile && $user->sellerProfile->status === 'pending') {
                 return back()->with('error', 'Votre demande est déjà en cours de traitement.');
            }

            // Determine if we should debit
            $shouldDebit = !$user->sellerProfile;

            // 1. Check Wallet Balance (before transaction)
            if ($shouldDebit && (!$wallet || !$wallet->hasEnoughBalance($amount))) {
                 return back()->withInput()->with('error', 'Solde insuffisant (5000 FCFA requis). Veuillez recharger votre wallet.');
            }

            // 2. Handle File Uploads (before transaction to fail fast)
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('seller/logos', 'public');
            }

            $kycPath = $request->file('kyc_document')->store('seller/kyc', 'public');

            // 3. WRAP IN DATABASE TRANSACTION
            \Illuminate\Support\Facades\DB::beginTransaction();
            
            try {
                // Debit Wallet
                if ($shouldDebit) {
                    $wallet->debit($amount);
                }

                // Create or Update Seller Profile
                $sellerProfile = $user->sellerProfile;

                if (!$sellerProfile) {
                    // Create new profile
                    $sellerProfile = $user->sellerProfile()->create([
                        'shop_name' => $request->shop_name,
                        'logo_path' => $logoPath,
                        'kyc_document_path' => $kycPath,
                        'status' => 'pending',
                        'commission_rate' => 10.0,
                        'licence_paid_at' => now(),
                        'is_active' => false,
                    ]);
                } else {
                    // Update existing (re-submission from Rejected)
                    $sellerProfile->update([
                        'shop_name' => $request->shop_name,
                        'logo_path' => $logoPath ?? $sellerProfile->logo_path,
                        'kyc_document_path' => $kycPath,
                        'status' => 'pending',
                        'rejection_reason' => null,
                        'is_active' => false,
                    ]);
                }
                
                // Update user KYC status
                $user->kyc_status = 'pending';
                $user->save();

                \Illuminate\Support\Facades\DB::commit();
                
                return redirect()->route('profile.show')->with('success', 'Votre demande est en cours de traitement. Vous recevrez une notification une fois approuvée.');

            } catch (\Illuminate\Database\QueryException $dbException) {
                \Illuminate\Support\Facades\DB::rollBack();
                
                // Log detailed DB error
                \Illuminate\Support\Facades\Log::error('Seller Registration DB Error', [
                    'error' => $dbException->getMessage(),
                    'sql' => $dbException->getSql() ?? 'N/A',
                    'bindings' => $dbException->getBindings() ?? [],
                    'user_id' => $user->id,
                ]);
                
                return back()->withInput()->with('error', 'Erreur de base de données: ' . $dbException->getMessage());
                
            } catch (\Exception $innerException) {
                \Illuminate\Support\Facades\DB::rollBack();
                
                \Illuminate\Support\Facades\Log::error('Seller Registration Error', [
                    'error' => $innerException->getMessage(),
                    'trace' => $innerException->getTraceAsString(),
                    'user_id' => $user->id,
                ]);
                
                return back()->withInput()->with('error', 'Erreur lors de la création du profil: ' . $innerException->getMessage());
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Seller Registration Outer Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Erreur technique: ' . $e->getMessage());
        }
    }

    /**
     * Renew seller license (one-click, no form).
     */
    public function renew()
    {
        $user = Auth::user();
        $profile = $user->sellerProfile;
        
        // Validation
        if (!$profile || $profile->status !== 'approved') {
            return back()->with('error', 'Profil vendeur non approuvé. Impossible de renouveler.');
        }
        
        // Check if renewal is needed/allowed (3 days before expiry or expired)
        $expiresAt = $profile->licence_expire_at;
        if ($expiresAt && $expiresAt->isFuture() && $expiresAt->diffInDays(now()) > 3) {
            $daysRemaining = $expiresAt->diffInDays(now());
            return back()->with('info', "Votre licence est encore valide pour {$daysRemaining} jours. Renouvellement disponible 3 jours avant expiration.");
        }
        
        $amount = new Amount(5000);
        $wallet = $user->wallet;
        
        // Check balance
        if (!$wallet || !$wallet->hasEnoughBalance($amount)) {
            return back()->with('error', 'Solde insuffisant (5000 FCFA requis). Veuillez recharger votre wallet.');
        }
        
        // Begin transaction
        \Illuminate\Support\Facades\DB::beginTransaction();
        
        try {
            // Debit wallet
            $wallet->debit($amount);
            
            // Update license dates
            $profile->update([
                'licence_paid_at' => now(),
                'licence_expire_at' => now()->addMonths(3),
            ]);
            
            // Refresh model to get updated values
            $profile->refresh();
            
            \Illuminate\Support\Facades\DB::commit();
            
            \Illuminate\Support\Facades\Log::info('License Renewed Successfully', [
                'user_id' => $user->id,
                'expires_at' => $profile->licence_expire_at,
            ]);
            
            $expiryDate = $profile->licence_expire_at ? $profile->licence_expire_at->format('d/m/Y') : 'N/A';
            
            return redirect()->route('profile.show')
                ->with('success', 'Licence renouvelée avec succès jusqu\'au ' . $expiryDate);
                
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            
            \Illuminate\Support\Facades\Log::error('License Renewal Error', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return back()->with('error', 'Erreur lors du renouvellement: ' . $e->getMessage());
        }
    }
}

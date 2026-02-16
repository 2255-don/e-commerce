
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
        
        // Check if renewal is needed/allowed
        $expiresAt = $profile->licence_expire_at;
        if ($expiresAt && $expiresAt->isFuture() && $expiresAt->diffInMonths(now()) > 3) {
            return back()->with('info', 'Votre licence est encore valide pour plus de 3 mois. Renouvellement disponible 3 mois avant expiration.');
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
            
            \Illuminate\Support\Facades\DB::commit();
            
            \Illuminate\Support\Facades\Log::info('License Renewed Successfully', [
                'user_id' => $user->id,
                'expires_at' => $profile->licence_expire_at,
            ]);
            
            return redirect()->route('profile.show')
                ->with('success', 'Licence renouvelée avec succès jusqu\'au ' . $profile->licence_expire_at->format('d/m/Y'));
                
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

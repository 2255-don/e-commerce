<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Modules\Identity\Entities\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Exception;

class KycController extends Controller
{
    /**
     * Display a listing of pending KYC requests.
     */
    public function index()
    {
        if (!Gate::allows('admin-access')) {
            abort(403);
        }

        try {
            $pendingUsers = User::where('kyc_status', 'pending')->latest()->get();
            $allUsers = User::whereNotNull('kyc_document_path')->latest()->get();

            return view('pages.admin.kyc-management', compact('pendingUsers', 'allUsers'));
        } catch (Exception $e) {
            Log::error('Admin KYC Index Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Erreur lors du chargement des demandes.']);
        }
    }

    /**
     * Approve a KYC request.
     */
    public function approve(User $user)
    {
        if (!Gate::allows('admin-access')) {
            abort(403);
        }

        try {
            Log::info('Admin KYC Approval START', [
                'user_id' => $user->id,
                'email' => $user->email,
                'has_seller_profile' => $user->sellerProfile ? 'YES' : 'NO',
            ]);

            // WRAP IN TRANSACTION
            \Illuminate\Support\Facades\DB::beginTransaction();
            
            try {
                // 1. Approve KYC
                $user->kyc_status = 'verified';
                $user->save();
                
                Log::info('User KYC status updated to verified');
                
                // 2. Approve Seller Profile if exists
                if ($user->sellerProfile) {
                    Log::info('Seller Profile found, updating...', [
                        'profile_id' => $user->sellerProfile->id,
                        'current_status' => $user->sellerProfile->status,
                    ]);

                    $user->sellerProfile->update([
                        'status' => 'approved',
                        'is_active' => true,
                        'approved_at' => now(),
                        'licence_expire_at' => now()->addMonths(3),
                    ]);
                    
                    // Refresh to verify
                    $user->sellerProfile->refresh();
                    
                    Log::info('Seller Profile updated successfully', [
                        'new_status' => $user->sellerProfile->status,
                        'is_active' => $user->sellerProfile->is_active,
                        'approved_at' => $user->sellerProfile->approved_at,
                    ]);
                } else {
                    Log::warning('No seller profile found for user', ['user_id' => $user->id]);
                }
                
                \Illuminate\Support\Facades\DB::commit();
                
                return back()->with('success', 'KYC validé avec succès. Le profil vendeur a été activé.');
                
            } catch (\Exception $innerException) {
                \Illuminate\Support\Facades\DB::rollBack();
                throw $innerException;
            }
            
        } catch (\Exception $e) {
            Log::error('Admin KYC Approve Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $user->id,
            ]);
            return back()->withErrors(['error' => 'Erreur lors de la validation: ' . $e->getMessage()]);
        }
    }

    /**
     * Reject a KYC request.
     */
    public function reject(User $user)
    {
        if (!Gate::allows('admin-access')) {
            abort(403);
        }

        try {
            Log::info('Admin KYC Rejection START', [
                'user_id' => $user->id,
                'has_seller_profile' => $user->sellerProfile ? 'YES' : 'NO',
            ]);

            // WRAP IN TRANSACTION
            \Illuminate\Support\Facades\DB::beginTransaction();
            
            try {
                $user->kyc_status = 'rejected';
                $user->save();
                
                // Reject Seller Profile
                if ($user->sellerProfile) {
                    $user->sellerProfile->update([
                        'status' => 'rejected',
                        'is_active' => false,
                    ]);
                    
                    Log::info('Seller Profile rejected', ['profile_id' => $user->sellerProfile->id]);
                }
                
                \Illuminate\Support\Facades\DB::commit();
                
                return back()->with('success', 'KYC rejeté. Le profil vendeur a été désactivé.');
                
            } catch (\Exception $innerException) {
                \Illuminate\Support\Facades\DB::rollBack();
                throw $innerException;
            }
            
        } catch (\Exception $e) {
            Log::error('Admin KYC Reject Error', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
            return back()->withErrors(['error' => 'Erreur lors du rejet: ' . $e->getMessage()]);
        }
    }
}

<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\KycService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Exception;

class AdminKycController extends Controller
{
    protected $kycService;

    public function __construct(KycService $kycService)
    {
        $this->kycService = $kycService;
    }

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
            $this->kycService->updateStatus($user, 'verified');
            return back()->with('status', 'kyc-approved');
        } catch (Exception $e) {
            Log::error('Admin KYC Approve Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Erreur lors de la validation.']);
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
            $this->kycService->updateStatus($user, 'rejected');
            return back()->with('status', 'kyc-rejected');
        } catch (Exception $e) {
            Log::error('Admin KYC Reject Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Erreur lors du rejet.']);
        }
    }
}

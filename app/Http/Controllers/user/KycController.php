<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Services\KycService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class KycController extends Controller
{
    protected $kycService;

    public function __construct(KycService $kycService)
    {
        $this->kycService = $kycService;
    }

    /**
     * Show the KYC upload form.
     */
    public function showForm()
    {
        try {
            $user = Auth::user();
            return view('pages.user.kyc', compact('user'));
        } catch (Exception $e) {
            Log::error('KYC Form Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Erreur technique.']);
        }
    }

    /**
     * Submit the KYC document.
     */
    public function store(Request $request)
    {
        $request->validate([
            'shop_name' => 'required|string|max:255',
            'kyc_document' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $this->kycService->submitKyc(
                Auth::user(),
                $request->file('kyc_document'),
                $request->shop_name
            );

            return redirect()->route('profile.show')->with('status', 'kyc-submitted');
        } catch (Exception $e) {
            Log::error('KYC Submission Error: ' . $e->getMessage(), [
                'user_id' => Auth::id()
            ]);
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}

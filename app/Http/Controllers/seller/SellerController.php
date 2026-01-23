<?php

namespace App\Http\Controllers\seller;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use App\Services\LicenseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class SellerController extends Controller
{
    protected $paymentService;
    protected $licenseService;

    public function __construct(PaymentService $paymentService, LicenseService $licenseService)
    {
        $this->paymentService = $paymentService;
        $this->licenseService = $licenseService;
    }

    /**
     * Show the license purchase page.
     */
    public function showLicenseForm()
    {
        try {
            $user = Auth::user();
            $profile = $user->sellerProfile;
            $wallet = $user->wallet;

            return view('pages.seller.license', compact('user', 'profile', 'wallet'));
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'affichage de la page de licence : ' . $e->getMessage(), [
                'user_id' => Auth::id()
            ]);
            return back()->withErrors(['error' => 'Erreur technique.']);
        }
    }

    /**
     * Process license purchase via Wallet.
     */
    public function purchaseWithWallet(Request $request)
    {
        try {
            $user = Auth::user();
            $wallet = $user->wallet;

            $this->paymentService->processPayment(
                $wallet,
                LicenseService::LICENSE_COST,
                'payment',
                'Achat/Renouvellement Licence Vendeur (3 mois)'
            );

            $this->licenseService->activateLicense($user);

            return redirect()->route('profile.show')->with('status', 'license-activated');
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'achat de licence via Wallet : ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('wallet.recharge')->withErrors(['error' => $e->getMessage()]);
        }
    }
}

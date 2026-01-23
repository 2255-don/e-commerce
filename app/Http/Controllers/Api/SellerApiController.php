<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use App\Services\LicenseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class SellerApiController extends Controller
{
    protected $paymentService;
    protected $licenseService;

    public function __construct(PaymentService $paymentService, LicenseService $licenseService)
    {
        $this->paymentService = $paymentService;
        $this->licenseService = $licenseService;
    }

    /**
     * Get seller profile info and status.
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $profile = $user->sellerProfile;

            return response()->json([
                'is_seller' => (bool)$profile,
                'is_active' => $profile ? $profile->isLicenseActive() : false,
                'licence_expire_at' => $profile ? $profile->licence_expire_at : null,
                'shop_name' => $profile ? $profile->shop_name : null,
                'license_cost' => LicenseService::LICENSE_COST
            ]);
        } catch (Exception $e) {
            Log::error('API Seller Info Error: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la récupération des infos vendeur.'], 500);
        }
    }

    /**
     * Purchase license via wallet API.
     */
    public function purchaseLicense(Request $request)
    {
        try {
            $user = $request->user();
            $wallet = $user->wallet;

            $this->paymentService->processPayment(
                $wallet,
                LicenseService::LICENSE_COST,
                'payment',
                'Achat Licence via API Mobile'
            );

            $profile = $this->licenseService->activateLicense($user);

            return response()->json([
                'message' => 'Licence activée avec succès.',
                'licence_expire_at' => $profile->licence_expire_at
            ]);
        } catch (Exception $e) {
            Log::error('API License Purchase Error: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}

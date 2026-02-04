<?php

namespace App\Http\Controllers\Web\Wallet;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Services\Payment\PaymentService;
use App\Services\Payment\MobileMoneyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class WalletController extends Controller
{
    protected $paymentService;
    protected $mmService;

    public function __construct(PaymentService $paymentService, MobileMoneyService $mmService)
    {
        $this->paymentService = $paymentService;
        $this->mmService = $mmService;
    }

    /**
     * Show the recharge page.
     */
    public function showRecharge()
    {
        try {
            $user = Auth::user();
            $wallet = $user->wallet ?? Wallet::create([
                'user_id' => $user->id,
                'balance' => 0,
                'currency' => 'XOF'
            ]);

            return view('pages.wallet.recharge', compact('wallet'));
        } catch (Exception $e) {
            Log::error('Erreur affichage recharge: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Erreur technique.']);
        }
    }

    /**
     * Process wallet recharge via Mock Mobile Money.
     */
    public function processRecharge(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'amount' => 'required|numeric|min:100',
            'provider' => 'nullable|string|in:airtel,vodacom,orange,africell',
            'otp' => 'nullable|string'
        ]);

        try {
            $user = Auth::user();
            $wallet = $user->wallet;

            // Call MobileMoneyService with provider
            $data = $this->mmService->processPayment(
                $validated['phone'], 
                $validated['amount'],
                $validated['provider'] ?? null,
                $validated['otp'] ?? '1234'
            );

            // Deposit funds to wallet
            $this->paymentService->deposit(
                $wallet,
                $validated['amount'],
                $data['transaction_id'],
                'Rechargement Wallet via ' . ($data['provider'] ?? 'Mobile Money')
            );

            return redirect()->route('profile.show')->with('status', 'wallet-recharged');

        } catch (Exception $e) {
            Log::error('Erreur rechargement wallet: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request' => $request->all()
            ]);
            
            // Return exception message directly (ex: "Solde insuffisant")
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}

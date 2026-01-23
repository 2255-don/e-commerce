<?php

namespace App\Http\Controllers\wallet;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Services\PaymentService;
use App\Services\MobileMoneyService;
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
        $request->validate([
            'phone' => 'required',
            'amount' => 'required|numeric|min:100',
        ]);

        try {
            $user = Auth::user();
            $wallet = $user->wallet;

            // Appel direct au Service au lieu d'un appel HTTP interne !
            // C'est beaucoup plus propre, rapide et fiable en local.
            $data = $this->mmService->processPayment($request->phone, $request->amount);

            // Dépôt
            $this->paymentService->deposit(
                $wallet,
                $request->amount,
                $data['transaction_id'],
                'Rechargement Wallet via Mobile Money'
            );

            return redirect()->route('profile.show')->with('status', 'wallet-recharged');

        } catch (Exception $e) {
            Log::error('Erreur rechargement wallet: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request' => $request->all()
            ]);
            
            // On renvoie directement le message de l'exception (ex: "Solde insuffisant")
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}

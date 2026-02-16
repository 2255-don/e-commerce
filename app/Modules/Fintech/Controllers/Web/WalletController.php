<?php

namespace Modules\Fintech\Controllers\Web;

use App\Http\Controllers\Controller;
use Modules\Fintech\Services\WalletService;
use Modules\Fintech\Services\PaymentGatewayService;
use Modules\Fintech\DTOs\RechargeWalletDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Wallet Controller
 * Gère les pages web du wallet
 */
class WalletController extends Controller
{
    public function __construct(
        private readonly WalletService $walletService,
        private readonly PaymentGatewayService $paymentGatewayService,
    ) {}
    
    /**
     * Afficher la page de rechargement
     */
    public function recharge()
    {
        try {
            $wallet = $this->walletService->getOrCreateWallet(Auth::id());
            
            return view('fintech::wallet.recharge', compact('wallet'));
            
        } catch (Exception $e) {
            Log::error('Erreur affichage recharge: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Erreur technique.']);
        }
    }
    
    /**
     * Traiter le rechargement wallet
     */
    public function processRecharge(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:100',
            'provider' => 'required|string|in:airtel,vodacom,orange,africell',
            'phone_number' => 'required|string',
            'otp' => 'nullable|string',
        ]);
        
        try {
            // 1. Initier paiement Mobile Money
            $paymentResult = $this->paymentGatewayService->initiateMobileMoneyPayment(
                phoneNumber: $validated['phone_number'],
                amount: $validated['amount'],
                provider: $validated['provider'],
                otp: $validated['otp'] ?? '1234'
            );
            
            // 2. Recharger wallet si paiement réussi
            if ($paymentResult['success']) {
                $dto = RechargeWalletDTO::fromRequest($validated, Auth::id());
                
                $transaction = $this->walletService->rechargeWallet(
                    dto: $dto,
                    transactionReference: $paymentResult['transaction_id']
                );
                
                return redirect()
                    ->route('profile.show')
                    ->with('success', "Wallet rechargé avec succès! Solde: {$transaction->formatted_amount}");
            }
            
            throw new Exception('Paiement Mobile Money échoué');
            
        } catch (Exception $e) {
            Log::error('Erreur rechargement wallet', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
            
            return back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }
    
    /**
     * Afficher l'historique des transactions
     */
    public function transactions(Request $request)
    {
        try {
            $wallet = $this->walletService->getOrCreateWallet(Auth::id());
            $balance = $this->walletService->getBalance(Auth::id());
            
            // Récupérer transactions avec pagination
            $transactionService = app(\Modules\Fintech\Services\TransactionService::class);
            $transactions = $transactionService->getUserTransactions(Auth::id(), 15);
            
            return view('fintech::wallet.transactions', compact('wallet', 'balance', 'transactions'));
            
        } catch (Exception $e) {
            Log::error('Erreur affichage transactions: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Erreur technique.']);
        }
    }
}

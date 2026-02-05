<?php

namespace Modules\Fintech\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Fintech\Services\WalletService;
use Modules\Fintech\Services\TransactionService;
use Modules\Fintech\DTOs\TransferDTO;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Exception;

/**
 * Wallet API Controller
 * Endpoints API pour le wallet
 */
class WalletApiController extends Controller
{
    public function __construct(
        private readonly WalletService $walletService,
        private readonly TransactionService $transactionService,
    ) {}
    
    /**
     * Obtenir le solde du wallet
     * GET /api/wallet/balance
     */
    public function balance(): JsonResponse
    {
        try {
            $wallet = $this->walletService->getOrCreateWallet(Auth::id());
            $balance = $this->walletService->getBalance(Auth::id());
            
            return response()->json([
                'success' => true,
                'data' => [
                    'wallet_id' => $wallet->id,
                    'balance' => $balance->getValue(),
                    'formatted_balance' => $balance->format(),
                    'currency' => $wallet->currency,
                    'status' => $wallet->status,
                ],
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Obtenir les transactions
     * GET /api/wallet/transactions
     */
    public function transactions(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $transactions = $this->transactionService->getUserTransactions(Auth::id(), $perPage);
            
            return response()->json([
                'success' => true,
                'data' => $transactions,
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Transférer des fonds
     * POST /api/wallet/transfer
     */
    public function transfer(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'receiver_wallet_id' => 'required|string|exists:wallets,id',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
        ]);
        
        try {
            $senderWallet = $this->walletService->getOrCreateWallet(Auth::id());
            
            $dto = TransferDTO::create(
                senderWalletId: $senderWallet->id,
                receiverWalletId: $validated['receiver_wallet_id'],
                amount: $validated['amount'],
                description: $validated['description'] ?? null
            );
            
            $transaction = $this->walletService->transferFunds($dto);
            
            return response()->json([
                'success' => true,
                'message' => 'Transfert effectué avec succès',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'reference' => $transaction->reference,
                    'amount' => $transaction->amount,
                    'status' => $transaction->status,
                ],
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
    
    /**
     * Obtenir une transaction par référence
     * GET /api/wallet/transactions/{reference}
     */
    public function getTransaction(string $reference): JsonResponse
    {
        try {
            $transaction = $this->transactionService->getTransactionByReference($reference);
            
            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction introuvable',
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $transaction,
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

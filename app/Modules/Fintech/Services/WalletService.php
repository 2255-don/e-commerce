<?php

namespace Modules\Fintech\Services;

use Modules\Fintech\Entities\Wallet;
use Modules\Fintech\Entities\Transaction;
use Modules\Fintech\Interfaces\WalletRepositoryInterface;
use Modules\Fintech\Interfaces\TransactionRepositoryInterface;
use Modules\Fintech\ValueObjects\Amount;
use Modules\Fintech\ValueObjects\Currency;
use Modules\Fintech\DTOs\RechargeWalletDTO;
use Modules\Fintech\DTOs\TransferDTO;
use DomainException;

/**
 * Wallet Service
 * Orchestre les opérations wallet
 */
class WalletService
{
    public function __construct(
        private readonly WalletRepositoryInterface $walletRepository,
        private readonly TransactionService $transactionService,
    ) {}
    
    /**
     * Obtenir ou créer le wallet d'un utilisateur
     */
    public function getOrCreateWallet(string $userId): Wallet
    {
        return $this->walletRepository->getOrCreateForUser($userId);
    }
    
    /**
     * Obtenir le solde d'un utilisateur
     */
    public function getBalance(string $userId): Amount
    {
        return $this->walletRepository->getBalance($userId);
    }
    
    /**
     * Recharger un wallet via transaction externe
     */
    public function rechargeWallet(RechargeWalletDTO $dto, string $transactionReference): Transaction
    {
        $wallet = $this->getOrCreateWallet($dto->userId);
        
        // Créer la transaction
        $transaction = $this->transactionService->createRechargeTransaction(
            receiverWalletId: $wallet->id,
            amount: $dto->amount,
            reference: $transactionReference,
            description: "Rechargement via {$dto->provider}",
            metadata: [
                'provider' => $dto->provider,
                'phone_number' => $dto->phoneNumber,
            ]
        );
        
        // Créditer le wallet
        $amount = new Amount($dto->amount);
        $wallet->credit($amount);
        
        // Marquer transaction comme complétée
        $transaction->markAsCompleted();
        
        return $transaction;
    }
    
    /**
     * Transférer des fonds entre wallets
     */
    public function transferFunds(TransferDTO $dto): Transaction
    {
        $senderWallet = $this->walletRepository->findById($dto->senderWalletId);
        $receiverWallet = $this->walletRepository->findById($dto->receiverWalletId);
        
        if (!$senderWallet) {
            throw new DomainException('Wallet émetteur introuvable');
        }
        
        if (!$receiverWallet) {
            throw new DomainException('Wallet destinataire introuvable');
        }
        
        $amount = new Amount($dto->amount);
        
        // Vérifier suffisamment de fonds
        if (!$senderWallet->hasEnoughBalance($amount)) {
            throw new DomainException('Fonds insuffisants pour le transfert');
        }
        
        // Créer transaction
        $transaction = $this->transactionService->createTransferTransaction(
            senderWalletId: $senderWallet->id,
            receiverWalletId: $receiverWallet->id,
            amount: $dto->amount,
            description: $dto->description
        );
        
        try {
            // Débiter émetteur
            $senderWallet->debit($amount);
            
            // Créditer destinataire
            $receiverWallet->credit($amount);
            
            // Marquer comme complété
            $transaction->markAsCompleted();
            
            return $transaction;
            
        } catch (\Exception $e) {
            // En cas d'erreur, marquer transaction comme échouée
            $transaction->markAsFailed($e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Débiter un wallet pour un paiement
     */
    public function debitForPayment(string $walletId, float $amount, string $description): Transaction
    {
        $wallet = $this->walletRepository->findById($walletId);
        
        if (!$wallet) {
            throw new DomainException('Wallet introuvable');
        }
        
        $amountVO = new Amount($amount);
        
        // Créer transaction de paiement
        $transaction = $this->transactionService->createPaymentTransaction(
            senderWalletId: $wallet->id,
            amount: $amount,
            description: $description
        );
        
        try {
            // Débiter wallet
            $wallet->debit($amountVO);
            
            // Marquer comme complété
            $transaction->markAsCompleted();
            
            return $transaction;
            
        } catch (\Exception $e) {
            $transaction->markAsFailed($e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Créditer un wallet (remboursement, etc.)
     */
    public function creditWallet(string $walletId, float $amount, string $description): Transaction
    {
        $wallet = $this->walletRepository->findById($walletId);
        
        if (!$wallet) {
            throw new DomainException('Wallet introuvable');
        }
        
        $amountVO = new Amount($amount);
        
        // Créer transaction de crédit/refund
        $transaction = $this->transactionService->createRefundTransaction(
            receiverWalletId: $wallet->id,
            amount: $amount,
            description: $description
        );
        
        // Créditer wallet
        $wallet->credit($amountVO);
        
        // Marquer comme complété
        $transaction->markAsCompleted();
        
        return $transaction;
    }
}

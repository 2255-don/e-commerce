<?php

namespace Modules\Fintech\Services;

use Modules\Fintech\Entities\Transaction;
use Modules\Fintech\Interfaces\TransactionRepositoryInterface;
use Modules\Fintech\DTOs\TransactionDTO;
use Modules\Fintech\ValueObjects\TransactionReference;
use Modules\Fintech\ValueObjects\TransactionType;

/**
 * Transaction Service
 * Gère la création et le traitement des transactions
 */
class TransactionService
{
    public function __construct(
        private readonly TransactionRepositoryInterface $transactionRepository,
    ) {}
    
    /**
     * Obtenir les transactions d'un utilisateur
     */
    public function getUserTransactions(string $userId, int $perPage = 15)
    {
        return $this->transactionRepository->getUserTransactions($userId, $perPage);
    }
    
    /**
     * Trouver une transaction par référence
     */
    public function getTransactionByReference(string $reference): ?Transaction
    {
        return $this->transactionRepository->findByReference($reference);
    }
    
    /**
     * Créer une transaction de rechargement
     */
    public function createRechargeTransaction(
        string $receiverWalletId,
        float $amount,
        string $reference,
        ?string $description = null,
        ?array $metadata = null
    ): Transaction {
        $dto = TransactionDTO::create(
            senderWalletId: null, // Pas d'émetteur pour rechargement
            receiverWalletId: $receiverWalletId,
            type: TransactionType::RECHARGE,
            amount: $amount,
            reference: $reference,
            description: $description ?? 'Rechargement wallet'
        );
        
        $transaction = $this->transactionRepository->create($dto);
        
        if ($metadata) {
            foreach ($metadata as $key => $value) {
                $transaction->addMetadata($key, $value);
            }
        }
        
        return $transaction;
    }
    
    /**
     * Créer une transaction de transfert
     */
    public function createTransferTransaction(
        string $senderWalletId,
        string $receiverWalletId,
        float $amount,
        ?string $description = null
    ): Transaction {
        $reference = TransactionReference::generate('TRF')->getValue();
        
        $dto = TransactionDTO::create(
            senderWalletId: $senderWalletId,
            receiverWalletId: $receiverWalletId,
            type: TransactionType::TRANSFER,
            amount: $amount,
            reference: $reference,
            description: $description ?? 'Transfert de fonds'
        );
        
        return $this->transactionRepository->create($dto);
    }
    
    /**
     * Créer une transaction de paiement
     */
    public function createPaymentTransaction(
        string $senderWalletId,
        float $amount,
        string $description
    ): Transaction {
        $reference = TransactionReference::generate('PAY')->getValue();
        
        $dto = TransactionDTO::create(
            senderWalletId: $senderWalletId,
            receiverWalletId: null, // Paiement externe
            type: TransactionType::PAYMENT,
            amount: $amount,
            reference: $reference,
            description: $description
        );
        
        return $this->transactionRepository->create($dto);
    }
    
    /**
     * Créer une transaction de remboursement
     */
    public function createRefundTransaction(
        string $receiverWalletId,
        float $amount,
        string $description
    ): Transaction {
        $reference = TransactionReference::generate('REF')->getValue();
        
        $dto = TransactionDTO::create(
            senderWalletId: null,
            receiverWalletId: $receiverWalletId,
            type: TransactionType::REFUND,
            amount: $amount,
            reference: $reference,
            description: $description
        );
        
        return $this->transactionRepository->create($dto);
    }
    
    /**
     * Obtenir les transactions récentes
     */
    public function getRecentTransactions(int $limit = 10)
    {
        return $this->transactionRepository->getRecent($limit);
    }
}

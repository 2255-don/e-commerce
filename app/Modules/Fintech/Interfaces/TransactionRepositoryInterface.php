<?php

namespace Modules\Fintech\Interfaces;

use Modules\Fintech\Entities\Transaction;
use Modules\Fintech\DTOs\TransactionDTO;

/**
 * Transaction Repository Interface
 */
interface TransactionRepositoryInterface
{
    /**
     * Trouver une transaction par référence
     */
    public function findByReference(string $reference): ?Transaction;
    
    /**
     * Obtenir toutes les transactions d'un wallet
     */
    public function getAllForWallet(string $walletId, array $filters = []);
    
    /**
     * Obtenir les transactions d'un utilisateur
     */
    public function getUserTransactions(string $userId, int $perPage = 15);
    
    /**
     * Créer une transaction
     */
    public function create(TransactionDTO $dto): Transaction;
    
    /**
     * Trouver une transaction par ID
     */
    public function findById(string $transactionId): ?Transaction;
    
    /**
     * Obtenir les transactions récentes
     */
    public function getRecent(int $limit = 10);
}

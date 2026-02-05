<?php

namespace Modules\Fintech\Repositories;

use Modules\Fintech\Entities\Transaction;
use Modules\Fintech\Entities\Wallet;
use Modules\Fintech\Interfaces\TransactionRepositoryInterface;
use Modules\Fintech\DTOs\TransactionDTO;

/**
 * Transaction Repository
 */
class TransactionRepository implements TransactionRepositoryInterface
{
    public function findByReference(string $reference): ?Transaction
    {
        return Transaction::where('reference', $reference)->first();
    }
    
    public function getAllForWallet(string $walletId, array $filters = [])
    {
        $query = Transaction::where(function ($q) use ($walletId) {
            $q->where('sender_wallet_id', $walletId)
              ->orWhere('receiver_wallet_id', $walletId);
        });
        
        // Appliquer filtres
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        
        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        
        return $query->orderBy('created_at', 'desc')
                    ->with(['senderWallet', 'receiverWallet'])
                    ->get();
    }
    
    public function getUserTransactions(string $userId, int $perPage = 15)
    {
        // Trouver le wallet de l'utilisateur
        $wallet = Wallet::where('user_id', $userId)->first();
        
        if (!$wallet) {
            return collect();
        }
        
        return Transaction::where(function ($q) use ($wallet) {
            $q->where('sender_wallet_id', $wallet->id)
              ->orWhere('receiver_wallet_id', $wallet->id);
        })
        ->with(['senderWallet.user', 'receiverWallet.user'])
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);
    }
    
    public function create(TransactionDTO $dto): Transaction
    {
        return Transaction::create($dto->toArray());
    }
    
    public function findById(string $transactionId): ?Transaction
    {
        return Transaction::find($transactionId);
    }
    
    public function getRecent(int $limit = 10)
    {
        return Transaction::with(['senderWallet.user', 'receiverWallet.user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}

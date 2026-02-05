<?php

namespace Modules\Fintech\Repositories;

use Modules\Fintech\Entities\Wallet;
use Modules\Fintech\Interfaces\WalletRepositoryInterface;
use Modules\Fintech\ValueObjects\Amount;
use Modules\Fintech\ValueObjects\Currency;

/**
 * Wallet Repository
 */
class WalletRepository implements WalletRepositoryInterface
{
    public function findByUserId(string $userId): ?Wallet
    {
        return Wallet::where('user_id', $userId)->first();
    }
    
    public function getBalance(string $userId): Amount
    {
        $wallet = $this->findByUserId($userId);
        
        if (!$wallet) {
            return Amount::zero();
        }
        
        return $wallet->getBalanceAsAmount();
    }
    
    public function createForUser(string $userId, Currency $currency): Wallet
    {
        return Wallet::create([
            'user_id' => $userId,
            'balance' => 0,
            'currency' => $currency->getValue(),
            'status' => 'active',
        ]);
    }
    
    public function getOrCreateForUser(string $userId): Wallet
    {
        $wallet = $this->findByUserId($userId);
        
        if (!$wallet) {
            $wallet = $this->createForUser($userId, Currency::fcfa());
        }
        
        return $wallet;
    }
    
    public function getAllWithUsers(int $perPage = 15)
    {
        return Wallet::with('user')->paginate($perPage);
    }
    
    public function findById(string $walletId): ?Wallet
    {
        return Wallet::find($walletId);
    }
}

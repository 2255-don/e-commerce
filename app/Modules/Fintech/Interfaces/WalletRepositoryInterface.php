<?php

namespace Modules\Fintech\Interfaces;

use Modules\Fintech\Entities\Wallet;
use Modules\Fintech\ValueObjects\Amount;
use Modules\Fintech\ValueObjects\Currency;

/**
 * Wallet Repository Interface
 */
interface WalletRepositoryInterface
{
    /**
     * Trouver un wallet par ID utilisateur
     */
    public function findByUserId(string $userId): ?Wallet;
    
    /**
     * Obtenir le solde d'un utilisateur
     */
    public function getBalance(string $userId): Amount;
    
    /**
     * Créer un wallet pour un utilisateur
     */
    public function createForUser(string $userId, Currency $currency): Wallet;
    
    /**
     * Obtenir ou créer un wallet
     */
    public function getOrCreateForUser(string $userId): Wallet;
    
    /**
     * Obtenir tous les wallets avec leurs utilisateurs
     */
    public function getAllWithUsers(int $perPage = 15);
    
    /**
     * Trouver un wallet par ID
     */
    public function findById(string $walletId): ?Wallet;
}

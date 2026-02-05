<?php

namespace Modules\Fintech\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Fintech\ValueObjects\Amount;
use Modules\Fintech\ValueObjects\Currency;
use Modules\Identity\Entities\User;
use DomainException;

/**
 * Wallet Entity (Aggregate Root)
 * Gère le portefeuille d'un utilisateur avec logique métier
 */
class Wallet extends Model
{
    use HasUuids;
    
    protected $fillable = [
        'user_id',
        'balance',
        'currency',
        'status',
    ];
    
    protected $casts = [
        'balance' => 'decimal:2',
    ];
    
    // ===========================================
    // DOMAIN LOGIC
    // ===========================================
    
    /**
     * Créditer le wallet
     */
    public function credit(Amount $amount): void
    {
        $currentBalance = new Amount((float) $this->balance);
        $newBalance = $currentBalance->add($amount);
        
        $this->balance = $newBalance->getValue();
        $this->save();
    }
    
    /**
     * Débiter le wallet
     * @throws DomainException si fonds insuffisants
     */
    public function debit(Amount $amount): void
    {
        $currentBalance = new Amount((float) $this->balance);
        
        if (!$this->hasEnoughBalance($amount)) {
            throw new DomainException(
                "Fonds insuffisants. Solde: {$currentBalance->format()}, Requis: {$amount->format()}"
            );
        }
        
        $newBalance = $currentBalance->subtract($amount);
        $this->balance = $newBalance->getValue();
        $this->save();
    }
    
    /**
     * Vérifier si le wallet a assez de fonds
     */
    public function hasEnoughBalance(Amount $amount): bool
    {
        $currentBalance = new Amount((float) $this->balance);
        return $currentBalance->canCover($amount);
    }
    
    /**
     * Obtenir le solde comme Value Object
     */
    public function getBalanceAsAmount(): Amount
    {
        return new Amount((float) $this->balance);
    }
    
    /**
     * Obtenir la devise comme Value Object
     */
    public function getCurrencyAsValueObject(): Currency
    {
        return new Currency($this->currency);
    }
    
    /**
     * Vérifier si le wallet est actif
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
    
    /**
     * Activer le wallet
     */
    public function activate(): void
    {
        $this->status = 'active';
        $this->save();
    }
    
    /**
     * Désactiver le wallet
     */
    public function deactivate(): void
    {
        $this->status = 'inactive';
        $this->save();
    }
    
    // ===========================================
    // RELATIONSHIPS
    // ===========================================
    
    /**
     * Utilisateur propriétaire du wallet
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Transactions où ce wallet est l'émetteur
     */
    public function transactionsAsSender(): HasMany
    {
        return $this->hasMany(Transaction::class, 'sender_wallet_id');
    }
    
    /**
     * Transactions où ce wallet est le destinataire
     */
    public function transactionsAsReceiver(): HasMany
    {
        return $this->hasMany(Transaction::class, 'receiver_wallet_id');
    }
    
    /**
     * Toutes les transactions du wallet (envoyées + reçues)
     */
    public function allTransactions()
    {
        return Transaction::where('sender_wallet_id', $this->id)
            ->orWhere('receiver_wallet_id', $this->id)
            ->orderBy('created_at', 'desc');
    }
}

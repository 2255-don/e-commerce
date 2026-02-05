<?php

namespace Modules\Fintech\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Fintech\ValueObjects\TransactionReference;
use Modules\Fintech\ValueObjects\TransactionType;
use Modules\Fintech\ValueObjects\TransactionStatus;
use Modules\Fintech\ValueObjects\Amount;

/**
 * Transaction Entity
 * Représente une transaction financière entre wallets
 */
class Transaction extends Model
{
    use HasUuids;
    
    protected $fillable = [
        'sender_wallet_id',
        'receiver_wallet_id',
        'type',
        'amount',
        'reference',
        'description',
        'status',
        'metadata',
        'completed_at',
        'failed_at',
        'failure_reason',
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
    ];
    
    // ===========================================
    // DOMAIN LOGIC
    // ===========================================
    
    /**
     * Marquer la transaction comme complétée
     */
    public function markAsCompleted(): void
    {
        $this->status = TransactionStatus::COMPLETED->value;
        $this->completed_at = now();
        $this->save();
    }
    
    /**
     * Marquer la transaction comme échouée
     */
    public function markAsFailed(string $reason = null): void
    {
        $this->status = TransactionStatus::FAILED->value;
        $this->failed_at = now();
        
        if ($reason) {
            $this->failure_reason = $reason;
        }
        
        $this->save();
    }
    
    /**
     * Marquer comme en traitement
     */
    public function markAsProcessing(): void
    {
        $this->status = TransactionStatus::PROCESSING->value;
        $this->save();
    }
    
    /**
     * Annuler la transaction
     */
    public function cancel(): void
    {
        if ($this->getStatusEnum()->isFinal()) {
            throw new \DomainException('Cannot cancel a finalized transaction');
        }
        
        $this->status = TransactionStatus::CANCELLED->value;
        $this->save();
    }
    
    /**
     * Vérifier si la transaction est finale
     */
    public function isFinal(): bool
    {
        return $this->getStatusEnum()->isFinal();
    }
    
    /**
     * Vérifier si la transaction peut être traitée
     */
    public function canBeProcessed(): bool
    {
        return $this->getStatusEnum()->isProcessable();
    }
    
    /**
     * Vérifier si c'est un crédit
     */
    public function isCredit(): bool
    {
        return $this->getTypeEnum()->isCredit();
    }
    
    /**
     * Vérifier si c'est un débit
     */
    public function isDebit(): bool
    {
        return $this->getTypeEnum()->isDebit();
    }
    
    /**
     * Obtenir le montant comme Value Object
     */
    public function getAmountAsValueObject(): Amount
    {
        return new Amount((float) $this->amount);
    }
    
    /**
     * Obtenir la référence comme Value Object
     */
    public function getReferenceAsValueObject(): TransactionReference
    {
        return TransactionReference::fromString($this->reference);
    }
    
    /**
     * Obtenir le type comme Enum
     */
    public function getTypeEnum(): TransactionType
    {
        return TransactionType::from($this->type);
    }
    
    /**
     * Obtenir le statut comme Enum
     */
    public function getStatusEnum(): TransactionStatus
    {
        return TransactionStatus::from($this->status);
    }
    
    /**
     * Ajouter des métadonnées
     */
    public function addMetadata(string $key, mixed $value): void
    {
        $metadata = $this->metadata ?? [];
        $metadata[$key] = $value;
        $this->metadata = $metadata;
        $this->save();
    }
    
    // ===========================================
    // RELATIONSHIPS
    // ===========================================
    
    /**
     * Wallet émetteur
     */
    public function senderWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'sender_wallet_id');
    }
    
    /**
     * Wallet destinataire
     */
    public function receiverWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'receiver_wallet_id');
    }
    
    // ===========================================
    // ACCESSORS & HELPERS
    // ===========================================
    
    /**
     * Format du montant pour affichage
     */
    public function getFormattedAmountAttribute(): string
    {
        return $this->getAmountAsValueObject()->format();
    }
    
    /**
     * Label du type pour affichage
     */
    public function getTypeLabelAttribute(): string
    {
        return $this->getTypeEnum()->label();
    }
    
    /**
     * Label du statut pour affichage
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->getStatusEnum()->label();
    }
    
    /**
     * Classe CSS pour le badge de statut
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return $this->getStatusEnum()->badgeClass();
    }
}

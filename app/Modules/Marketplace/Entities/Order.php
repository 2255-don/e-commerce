<?php

namespace Modules\Marketplace\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Marketplace\ValueObjects\Price;
use Modules\Marketplace\ValueObjects\OrderStatus;
use Modules\Marketplace\ValueObjects\PaymentMethod;
use Modules\Identity\Entities\User;
use DomainException;

/**
 * Order Entity (Aggregate Root)
 * Gère une commande
 */
class Order extends Model
{
    use HasUuids;
    
    protected $fillable = [
        'buyer_id',
        'reference',
        'total_amount',
        'status',
        'payment_method',
        'payment_status',
        'delivery_status',
        'delivery_code',
        'notes',
        'paid_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
    ];
    
    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];
    
    // ===========================================
    // DOMAIN LOGIC
    // ===========================================
    
    /**
     * Marquer comme payée
     */
    public function markAsPaid(): void
    {
        if ($this->payment_status === 'paid') {
            throw new DomainException('Commande déjà payée');
        }
        
        $this->payment_status = 'paid';
        $this->paid_at = now();
        $this->save();
    }
    
    /**
     * Marquer en traitement
     */
    public function markAsProcessing(): void
    {
        $currentStatus = $this->getStatusEnum();
        
        if ($currentStatus->isFinal()) {
            throw new DomainException('Impossible de modifier une commande finalisée');
        }
        
        $this->status = OrderStatus::PROCESSING->value;
        $this->save();
    }
    
    /**
     * Marquer comme complétée
     */
    public function markAsCompleted(): void
    {
        $currentStatus = $this->getStatusEnum();
        
        if ($currentStatus === OrderStatus::COMPLETED) {
            throw new DomainException('Commande déjà complétée');
        }
        
        if ($currentStatus->isFinal()) {
            throw new DomainException('Impossible de compléter une commande annulée/remboursée');
        }
        
        $this->status = OrderStatus::COMPLETED->value;
        $this->completed_at = now();
        $this->save();
    }
    
    /**
     * Annuler la commande
     */
    public function cancel(string $reason): void
    {
        if (!$this->canBeCancelled()) {
            throw new DomainException('Cette commande ne peut pas être annulée');
        }
        
        $this->status = OrderStatus::CANCELLED->value;
        $this->cancelled_at = now();
        $this->cancellation_reason = $reason;
        $this->save();
    }
    
    /**
     * Vérifier si peut être annulée
     */
    public function canBeCancelled(): bool
    {
        return $this->getStatusEnum()->canBeCancelled();
    }
    
    /**
     * Vérifier si peut être remboursée
     */
    public function canBeRefunded(): bool
    {
        return $this->getStatusEnum()->canBeRefunded() && 
               $this->payment_status === 'paid';
    }
    
    /**
     * Calculer le montant total
     */
    public function getTotalAmount(): Price
    {
        return Price::fromFloat((float) $this->total_amount);
    }
    
    /**
     * Obtenir le statut comme Enum
     */
    public function getStatusEnum(): OrderStatus
    {
        return OrderStatus::from($this->status);
    }
    
    /**
     * Obtenir la méthode de paiement comme Enum
     */
    public function getPaymentMethodEnum(): PaymentMethod
    {
        return PaymentMethod::from($this->payment_method);
    }
    
    // ===========================================
    // RELATIONSHIPS
    // ===========================================
    
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
    
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    
    // ===========================================
    // ACCESSORS
    // ===========================================
    
    /**
     * Total formaté
     */
    public function getFormattedTotalAttribute(): string
    {
        return $this->getTotalAmount()->format();
    }
    
    /**
     * Label du statut
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->getStatusEnum()->label();
    }
    
    /**
     * Badge class du statut
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return $this->getStatusEnum()->badgeClass();
    }
    
    /**
     * Label méthode de paiement
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return $this->getPaymentMethodEnum()->label();
    }
}

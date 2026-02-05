<?php

namespace Modules\Marketplace\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Marketplace\ValueObjects\Price;

/**
 * OrderItem Entity
 * Item dans une commande
 */
class OrderItem extends Model
{
    use HasUuids;
    
    protected $fillable = [
        'order_id',
        'product_id',
        'seller_id',
        'quantity',
        'price_at_purchase',
        'subtotal',
    ];
    
    protected $casts = [
        'quantity' => 'integer',
        'price_at_purchase' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];
    
    // ===========================================
    // DOMAIN LOGIC
    // ===========================================
    
    /**
     * Calculer le sous-total
     */
    public function calculateSubtotal(): void
    {
        $price = Price::fromFloat((float) $this->price_at_purchase);
        $total = $price->multiply($this->quantity);
        
        $this->subtotal = $total->getAmount();
        $this->save();
    }
    
    /**
     * Obtenir le sous-total comme Price VO
     */
    public function getSubtotalAsPrice(): Price
    {
        return Price::fromFloat((float) $this->subtotal);
    }
    
    // ===========================================
    // RELATIONSHIPS
    // ===========================================
    
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
    
    // ===========================================
    // ACCESSORS
    // ===========================================
    
    /**
     * Sous-total formaté
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return $this->getSubtotalAsPrice()->format();
    }
}

<?php

namespace Modules\Marketplace\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Marketplace\ValueObjects\Price;

/**
 * CartItem Entity
 * Item dans un panier
 */
class CartItem extends Model
{
    use HasUuids;
    
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'price_at_addition',
    ];
    
    protected $casts = [
        'quantity' => 'integer',
        'price_at_addition' => 'decimal:2',
    ];
    
    // ===========================================
    // DOMAIN LOGIC
    // ===========================================
    
    /**
     * Mettre à jour la quantité
     */
    public function updateQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
        $this->save();
    }
    
    /**
     * Calculer le total pour cet item
     */
    public function getTotal(): Price
    {
        $price = Price::fromFloat((float) $this->price_at_addition);
        return $price->multiply($this->quantity);
    }
    
    // ===========================================
    // RELATIONSHIPS
    // ===========================================
    
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }
    
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    // ===========================================
    // ACCESSORS
    // ===========================================
    
    /**
     * Total formaté
     */
    public function getFormattedTotalAttribute(): string
    {
        return $this->getTotal()->format();
    }

    /**
     * Subtotal attribute for view access
     */
    public function getSubtotalAttribute()
    {
        return $this->getTotal()->getAmount();
    }
}

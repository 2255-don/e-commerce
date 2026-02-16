<?php

namespace Modules\Marketplace\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Marketplace\ValueObjects\Price;
use Modules\Identity\Entities\User;
use DomainException;

/**
 * Cart Entity (Aggregate Root)
 * Gère le panier d'un utilisateur
 */
class Cart extends Model
{
    use HasUuids;
    
    protected $fillable = [
        'user_id',
        'session_id',
    ];
    
    // ===========================================
    // DOMAIN LOGIC
    // ===========================================
    
    /**
     * Ajouter un produit au panier
     */
    public function addItem(Product $product, int $quantity): CartItem
    {
        // Vérifier stock disponible
        if (!$product->isInStock($quantity)) {
            throw new DomainException('Stock insuffisant pour ce produit');
        }
        
        // Vérifier si produit déjà dans le panier
        $existingItem = $this->items()->where('product_id', $product->id)->first();
        
        if ($existingItem) {
            // Mettre à jour la quantité
            $newQuantity = $existingItem->quantity + $quantity;
            
            if (!$product->isInStock($newQuantity)) {
                throw new DomainException('Stock insuffisant pour cette quantité totale');
            }
            
            $existingItem->updateQuantity($newQuantity);
            return $existingItem;
        }
        
        // Créer nouvel item
        return $this->items()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price_at_addition' => $product->price,
        ]);
    }
    
    /**
     * Retirer un item du panier
     */
    public function removeItem(string $itemId): void
    {
        $item = $this->items()->find($itemId);
        
        if (!$item) {
            throw new DomainException('Item introuvable dans le panier');
        }
        
        $item->delete();
    }
    
    /**
     * Mettre à jour la quantité d'un item
     */
    public function updateQuantity(string $itemId, int $quantity): void
    {
        if ($quantity < 1) {
            throw new DomainException('La quantité doit être au moins 1');
        }
        
        $item = $this->items()->find($itemId);
        
        if (!$item) {
            throw new DomainException('Item introuvable dans le panier');
        }
        
        // Vérifier stock disponible
        if (!$item->product->isInStock($quantity)) {
            throw new DomainException('Stock insuffisant');
        }
        
        $item->updateQuantity($quantity);
    }
    
    /**
     * Vider le panier
     */
    public function clear(): void
    {
        $this->items()->delete();
    }
    
    /**
     * Calculer le montant total
     */
    public function getTotalAmount(): Price
    {
        $total = Price::zero();
        
        foreach ($this->items as $item) {
            $itemTotal = $item->getTotal();
            $total = $total->add($itemTotal);
        }
        
        return $total;
    }
    
    /**
     * Obtenir le nombre total d'items
     */
    public function getItemsCount(): int
    {
        return $this->items->sum('quantity');
    }
    
    /**
     * Vérifier si le panier est vide
     */
    public function isEmpty(): bool
    {
        return $this->items()->count() === 0;
    }
    
    /**
     * Valider tout le panier (stock disponible pour tous les items)
     */
    public function validate(): bool
    {
        foreach ($this->items as $item) {
            if (!$item->product->isInStock($item->quantity)) {
                return false;
            }
        }
        
        return true;
    }
    
    // ===========================================
    // RELATIONSHIPS
    // ===========================================
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
    
    /**
     * Total attribute for view access
     */
    public function getTotalAttribute()
    {
        return $this->getTotalAmount()->getAmount();
    }
}

<?php

namespace Modules\Marketplace\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Marketplace\ValueObjects\Price;
use Modules\Marketplace\ValueObjects\Stock;
use Modules\Marketplace\ValueObjects\SKU;
use Modules\Marketplace\ValueObjects\Slug;
use Modules\Marketplace\ValueObjects\ProductType;
use Modules\Identity\Entities\User;
use DomainException;

/**
 * Product Entity (Aggregate Root)
 * Gère un produit avec stock, prix et logique métier
 */
class Product extends Model
{
    use HasUuids;
    
    protected $fillable = [
        'seller_id',
        'category_id',
        'title',
        'slug',
        'sku',
        'description',
        'price',
        'stock_quantity',
        'type',
        'is_active',
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'is_active' => 'boolean',
    ];
    
    // ===========================================
    // DOMAIN LOGIC
    // ===========================================
    
    /**
     * Diminuer le stock
     */
    public function decreaseStock(int $quantity): void
    {
        $currentStock = Stock::fromInt($this->stock_quantity);
        
        if (!$currentStock->isAvailable($quantity)) {
            throw new DomainException("Stock insuffisant. Disponible: {$currentStock->getQuantity()}, Requis: {$quantity}");
        }
        
        $newStock = $currentStock->decrease($quantity);
        $this->stock_quantity = $newStock->getQuantity();
        $this->save();
    }
    
    /**
     * Augmenter le stock
     */
    public function increaseStock(int $quantity): void
    {
        $currentStock = Stock::fromInt($this->stock_quantity);
        $newStock = $currentStock->increase($quantity);
        
        $this->stock_quantity = $newStock->getQuantity();
        $this->save();
    }
    
    /**
     * Vérifier si en stock
     */
    public function isInStock(int $quantity = 1): bool
    {
        $stock = Stock::fromInt($this->stock_quantity);
        return $stock->isAvailable($quantity);
    }
    
    /**
     * Obtenir le stock comme Value Object
     */
    public function getStockAsValueObject(): Stock
    {
        return Stock::fromInt($this->stock_quantity);
    }
    
    /**
     * Mettre à jour le prix
     */
    public function updatePrice(Price $newPrice): void
    {
        $this->price = $newPrice->getAmount();
        $this->save();
    }
    
    /**
     * Obtenir le prix comme Value Object
     */
    public function getPriceAsValueObject(): Price
    {
        return Price::fromFloat((float) $this->price);
    }
    
    /**
     * Calculer prix total pour quantité
     */
    public function calculateTotal(int $quantity): Price
    {
        $price = $this->getPriceAsValueObject();
        return $price->multiply($quantity);
    }
    
    /**
     * Activer le produit
     */
    public function activate(): void
    {
        $this->is_active = true;
        $this->save();
    }
    
    /**
     * Désactiver le produit
     */
    public function deactivate(): void
    {
        $this->is_active = false;
        $this->save();
    }
    
    /**
     * Vérifier si le produit appartient à un vendeur
     */
    public function isOwnedBy(string $sellerId): bool
    {
        return $this->seller_id === $sellerId;
    }
    
    /**
     * Obtenir le type comme Enum
     */
    public function getTypeEnum(): ProductType
    {
        return ProductType::from($this->type);
    }
    
    /**
     * Générer un SKU unique
     */
    public function generateSKU(): void
    {
        if (empty($this->sku)) {
            $sku = SKU::generate('PRD');
            $this->sku = $sku->getValue();
            $this->save();
        }
    }
    
    /**
     * Générer un slug depuis le titre
     */
    public function generateSlug(): void
    {
        $slug = Slug::fromString($this->title);
        
        // Vérifier unicité
        $count = static::where('slug', $slug->getValue())
            ->where('id', '!=', $this->id ?? '')
            ->count();
        
        if ($count > 0) {
            $slug = Slug::generate($this->title, uniqid());
        }
        
        $this->slug = $slug->getValue();
        $this->save();
    }
    
    // ===========================================
    // RELATIONSHIPS
    // ===========================================
    
    /**
     * Vendeur du produit
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
    
    /**
     * Catégorie du produit
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    
    /**
     * Images du produit
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('display_order');
    }
    
    // ===========================================
    // ACCESSORS
    // ===========================================
    
    /**
     * Prix formaté pour affichage
     */
    public function getFormattedPriceAttribute(): string
    {
        return $this->getPriceAsValueObject()->format();
    }
    
    /**
     * Stock status
     */
    public function getStockStatusAttribute(): string
    {
        return $this->getStockAsValueObject()->getStatus();
    }
    
    /**
     * Stock badge class
     */
    public function getStockBadgeClassAttribute(): string
    {
        return $this->getStockAsValueObject()->getBadgeClass();
    }
    
    /**
     * URL de la miniature
     */
    public function getThumbnailUrlAttribute(): string
    {
        $primary = $this->images()->where('is_primary', true)->first();
        return $primary 
            ? asset('storage/' . $primary->image_path) 
            : asset('assets/img/products/default-product.png');
    }
}

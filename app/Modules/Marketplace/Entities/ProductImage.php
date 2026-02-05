<?php

namespace Modules\Marketplace\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ProductImage Entity
 * Image d'un produit
 */
class ProductImage extends Model
{
    use HasUuids;
    
    protected $fillable = [
        'product_id',
        'image_path',
        'is_primary',
        'display_order',
    ];
    
    protected $casts = [
        'is_primary' => 'boolean',
        'display_order' => 'integer',
    ];
    
    // ===========================================
    // DOMAIN LOGIC
    // ===========================================
    
    /**
     * Définir comme image principale
     */
    public function setAsPrimary(): void
    {
        // Retirer primary des autres images du produit
        static::where('product_id', $this->product_id)
            ->where('id', '!=', $this->id)
            ->update(['is_primary' => false]);
        
        $this->is_primary = true;
        $this->save();
    }
    
    /**
     * Obtenir l'URL complète
     */
    public function getFullUrl(): string
    {
        return asset('storage/' . $this->image_path);
    }
    
    // ===========================================
    // RELATIONSHIPS
    // ===========================================
    
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

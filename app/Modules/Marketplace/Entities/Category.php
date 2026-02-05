<?php

namespace Modules\Marketplace\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Marketplace\ValueObjects\Slug;

/**
 * Category Entity
 * Catégories hiérarchiques pour produits
 */
class Category extends Model
{
    use HasUuids;
    
    protected $fillable = [
        'name',
        'slug',
        'icon_path',
        'parent_id',
    ];
    
    // ===========================================
    // DOMAIN LOGIC
    // ===========================================
    
    /**
     * Vérifier si c'est une catégorie racine
     */
    public function isRoot(): bool
    {
        return $this->parent_id === null;
    }
    
    /**
     * Vérifier si a des enfants
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }
    
    /**
     * Obtenir tous les parents dans


 la hiérarchie
     */
    public function getAncestors(): array
    {
        $ancestors = [];
        $current = $this->parent;
        
        while ($current) {
            $ancestors[] = $current;
            $current = $current->parent;
        }
        
        return array_reverse($ancestors);
    }
    
    /**
     * Générer slug depuis le nom
     */
    public function generateSlug(): void
    {
        $slug = Slug::fromString($this->name);
        
        // Vérifier unicité
        $count = static::where('slug', $slug->getValue())
            ->where('id', '!=', $this->id ?? '')
            ->count();
        
        if ($count > 0) {
            $slug = Slug::generate($this->name, uniqid());
        }
        
        $this->slug = $slug->getValue();
        $this->save();
    }
    
    // ===========================================
    // RELATIONSHIPS
    // ===========================================
    
    /**
     * Catégorie parente
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
    
    /**
     * Sous-catégories
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
    
    /**
     * Produits de cette catégorie
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
    
    // ===========================================
    // ACCESSORS
    // ===========================================
    
    /**
     * Nombre de produits
     */
    public function getProductsCountAttribute(): int
    {
        return $this->products()->count();
    }
}

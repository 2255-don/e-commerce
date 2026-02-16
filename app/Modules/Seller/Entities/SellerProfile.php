<?php

namespace Modules\Seller\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Seller\ValueObjects\LicenseNumber;
use Modules\Seller\ValueObjects\BusinessName;
use Modules\Seller\ValueObjects\SellerStatus;
use Modules\Seller\ValueObjects\CommissionRate;
use Modules\Identity\Entities\User;
use Modules\Marketplace\Entities\Product;
use DomainException;

/**
 * SellerProfile Entity (Aggregate Root)
 * Profil vendeur avec gestion licence et statut
 */
class SellerProfile extends Model
{
    use HasUuids;
    
    protected $fillable = [
        'user_id',
        'shop_name',
        'logo_path',
        'kyc_document_path',
        'business_name',
        'license_number',
        'commission_rate',
        'status',
        'licence_paid_at', // Fixed: was license_paid_at
        'licence_expire_at', // Fixed: was license_expire_at
        'approved_at',
        'suspended_at',
        'rejection_reason',
        'suspension_reason',
        'is_active',
    ];
    
    protected $casts = [
        'commission_rate' => 'decimal:2',
        'licence_paid_at' => 'datetime', // Fixed: was license_paid_at
        'licence_expire_at' => 'datetime', // Fixed: was license_expire_at
        'approved_at' => 'datetime',
        'suspended_at' => 'datetime',
        'is_active' => 'boolean',
    ];
    
    // ===========================================
    // DOMAIN LOGIC
    // ===========================================
    
    /**
     * Approuver le profil vendeur
     */
    public function approve(): void
    {
        $currentStatus = $this->getStatusEnum();
        
        if ($currentStatus === SellerStatus::APPROVED) {
            throw new DomainException('Profil déjà approuvé');
        }
        
        $this->status = SellerStatus::APPROVED->value;
        $this->approved_at = now();
        $this->is_active = true;
        $this->rejection_reason = null;
        $this->save();
    }
    
    /**
     * Suspendre le profil vendeur
     */
    public function suspend(string $reason): void
    {
        if (!$this->isActive()) {
            throw new DomainException('Impossible de suspendre un profil non actif');
        }
        
        $this->status = SellerStatus::SUSPENDED->value;
        $this->suspended_at = now();
        $this->suspension_reason = $reason;
        $this->is_active = false;
        $this->save();
    }
    
    /**
     * Rejeter la demande vendeur
     */
    public function reject(string $reason): void
    {
        $currentStatus = $this->getStatusEnum();
        
        if ($currentStatus !== SellerStatus::PENDING) {
            throw new DomainException('Seules les demandes en attente peuvent être rejetées');
        }
        
        $this->status = SellerStatus::REJECTED->value;
        $this->rejection_reason = $reason;
        $this->is_active = false;
        $this->save();
    }
    
    /**
     * Mettre à jour la licence
     */
    public function updateLicense(LicenseNumber $license, ?\DateTime $expiryDate = null): void
    {
        $this->license_number = $license->getValue();
        
        if ($expiryDate) {
            $this->licence_expire_at = $expiryDate;
        } else {
            // Set license expiry to 1 year from now if not provided
            $this->licence_expire_at = now()->addYear();
        }
        
        $this->save();
    }
    
    /**
     * Générer un numéro de licence
     */
    public function generateLicense(): void
    {
        if (empty($this->license_number)) {
            $license = LicenseNumber::generate();
            $this->license_number = $license->getValue();
            // Set initial license paid date and expiry
            if (!$this->licence_paid_at) {
                $this->licence_paid_at = now();
            }
            if (!$this->licence_expire_at) {
                $this->licence_expire_at = now()->addYear();
            }
            $this->save();
        }
    }
    
    /**
     * Vérifier si le vendeur est actif
     */
    public function isActive(): bool
    {
        return $this->is_active && 
               $this->getStatusEnum() === SellerStatus::APPROVED &&
               $this->isLicenseValid();
    }
    
    /**
     * Vérifier si peut vendre
     */
    public function canSell(): bool
    {
        return $this->isActive();
    }
    
    /**
     * Vérifier si la licence est valide
     */
    public function isLicenseValid(): bool
    {
        return $this->licence_expire_at && 
               $this->licence_expire_at->isFuture();
    }

    /**
     * Alias pour isLicenseValid (pour compatibilité)
     */
    public function isLicenseActive(): bool
    {
        return $this->isLicenseValid();
    }
    
    /**
     * Obtenir le statut comme Enum
     */
    public function getStatusEnum(): SellerStatus
    {
        return SellerStatus::from($this->status ?? 'pending');
    }
    
    /**
     * Obtenir le taux de commission comme VO
     */
    public function getCommissionRateAsVO(): CommissionRate
    {
        return CommissionRate::fromFloat((float) ($this->commission_rate ?? 10.0));
    }
    
    /**
     * Calculer la commission sur un montant
     */
    public function calculateCommission(float $amount): float
    {
        return $this->getCommissionRateAsVO()->calculateCommission($amount);
    }
    
    // ===========================================
    // RELATIONSHIPS
    // ===========================================
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'seller_id', 'user_id');
    }
    
    // ===========================================
    // ACCESSORS
    // ===========================================
    
    public function getStatusLabelAttribute(): string
    {
        return $this->getStatusEnum()->label();
    }
    
    public function getStatusBadgeClassAttribute(): string
    {
        return $this->getStatusEnum()->badgeClass();
    }
    
    public function getFormattedCommissionAttribute(): string
    {
        return $this->getCommissionRateAsVO()->format();
    }
    
    public function getFormattedLicenseExpiryAttribute(): string
    {
        if (!$this->licence_expire_at) {
            return 'Non définie';
        }
        
        // Ensure it's a Carbon instance
        $date = $this->licence_expire_at instanceof \Carbon\Carbon 
            ? $this->licence_expire_at 
            : \Carbon\Carbon::parse($this->licence_expire_at);
            
        return $date->format('d/m/Y');
    }
}

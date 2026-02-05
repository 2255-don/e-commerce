<?php

namespace Modules\Seller\Services;

use Modules\Seller\Interfaces\SellerProfileRepositoryInterface;
use Modules\Seller\DTOs\CreateSellerProfileDTO;
use Modules\Seller\DTOs\UpdateSellerProfileDTO;
use Modules\Seller\Entities\SellerProfile;

class SellerProfileService
{
    public function __construct(
        private readonly SellerProfileRepositoryInterface $sellerProfileRepository,
    ) {}
    
    public function createProfile(CreateSellerProfileDTO $dto): SellerProfile
    {
        $profile = $this->sellerProfileRepository->create($dto->toArray());
        
        // Générer licence automatiquement
        $profile->generateLicense();
        
        return $profile->fresh();
    }
    
    public function updateProfile(string $id, UpdateSellerProfileDTO $dto): SellerProfile
    {
        return $this->sellerProfileRepository->update($id, $dto->toArray());
    }
    
    public function approveProfile(string $id): SellerProfile
    {
        $profile = $this->sellerProfileRepository->findById($id);
        
        if (!$profile) {
            throw new \DomainException('Profil vendeur introuvable');
        }
        
        $profile->approve();
        
        return $profile;
    }
    
    public function suspendProfile(string $id, string $reason): SellerProfile
    {
        $profile = $this->sellerProfileRepository->findById($id);
        
        if (!$profile) {
            throw new \DomainException('Profil vendeur introuvable');
        }
        
        $profile->suspend($reason);
        
        return $profile;
    }
    
    public function rejectProfile(string $id, string $reason): SellerProfile
    {
        $profile = $this->sellerProfileRepository->findById($id);
        
        if (!$profile) {
            throw new \DomainException('Profil vendeur introuvable');
        }
        
        $profile->reject($reason);
        
        return $profile;
    }
    
    public function getProfileByUserId(string $userId): ?SellerProfile
    {
        return $this->sellerProfileRepository->findByUserId($userId);
    }
}

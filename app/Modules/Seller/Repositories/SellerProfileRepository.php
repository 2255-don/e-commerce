<?php

namespace Modules\Seller\Repositories;

use Modules\Seller\Entities\SellerProfile;
use Modules\Seller\Interfaces\SellerProfileRepositoryInterface;

class SellerProfileRepository implements SellerProfileRepositoryInterface
{
    public function findById(string $id): ?SellerProfile
    {
        return SellerProfile::with('user')->find($id);
    }
    
    public function findByUserId(string $userId): ?SellerProfile
    {
        return SellerProfile::where('user_id', $userId)->first();
    }
    
    public function findByLicense(string $license): ?SellerProfile
    {
        return SellerProfile::where('license_number', $license)->first();
    }
    
    public function getPendingProfiles(int $perPage = 15)
    {
        return SellerProfile::where('status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
    
    public function getApprovedSellers(int $perPage = 15)
    {
        return SellerProfile::where('status', 'approved')
            ->where('is_active', true)
            ->with('user')
            ->orderBy('shop_name')
            ->paginate($perPage);
    }
    
    public function create(array $data): SellerProfile
    {
        return SellerProfile::create($data);
    }
    
    public function update(string $id, array $data): SellerProfile
    {
        $profile = $this->findById($id);
        $profile->update($data);
        return $profile->fresh();
    }
}

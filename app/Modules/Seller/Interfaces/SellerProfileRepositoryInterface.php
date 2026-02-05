<?php

namespace Modules\Seller\Interfaces;

use Modules\Seller\Entities\SellerProfile;

interface SellerProfileRepositoryInterface
{
    public function findById(string $id): ?SellerProfile;
    public function findByUserId(string $userId): ?SellerProfile;
    public function findByLicense(string $license): ?SellerProfile;
    public function getPendingProfiles(int $perPage = 15);
    public function getApprovedSellers(int $perPage = 15);
    public function create(array $data): SellerProfile;
    public function update(string $id, array $data): SellerProfile;
}

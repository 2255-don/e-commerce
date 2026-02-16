<?php

use Modules\Identity\Entities\User;

echo "=== CHECKING USER WITH PENDING SELLER REQUEST ===\n";

// Find user with kyc_status = pending  
$pendingUser = User::where('kyc_status', 'pending')->first();

if (!$pendingUser) {
    echo "No user with pending KYC found. Creating test scenario...\n";
    $pendingUser = User::first();
    if ($pendingUser) {
        $pendingUser->kyc_status = 'pending';
        $pendingUser->save();
    }
}

if ($pendingUser) {
    echo "User ID: {$pendingUser->id}\n";
    echo "Email: {$pendingUser->email}\n";
    echo "KYC Status: {$pendingUser->kyc_status}\n";
    
    if ($pendingUser->sellerProfile) {
        echo "\n=== Associated Seller Profile EXISTS ===\n";
        echo "Profile ID: {$pendingUser->sellerProfile->id}\n";
        echo "Shop Name: {$pendingUser->sellerProfile->shop_name}\n";
        echo "Status: {$pendingUser->sellerProfile->status}\n";
        echo "Is Active: " . ($pendingUser->sellerProfile->is_active ? 'YES' : 'NO') . "\n";
    } else {
        echo "\n=== NO Seller Profile Associated ===\n";
        echo "This is the problem - profile was not created!\n";
    }
}

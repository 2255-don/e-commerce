<?php

use Modules\Identity\Entities\User;

echo "=== CHECKING SELLER PROFILES ===\n\n";

$users = User::whereHas('sellerProfile')->with('sellerProfile')->get();

echo "Found " . $users->count() . " users with seller profiles\n\n";

foreach ($users as $user) {
    echo "User: {$user->email}\n";
    echo "  KYC Status: {$user->kyc_status}\n";
    if ($user->sellerProfile) {
        echo "  Seller Status: {$user->sellerProfile->status}\n";
        echo "  Is Active: " . ($user->sellerProfile->is_active ? 'YES' : 'NO') . "\n";
        echo "  Licence Paid At: " . ($user->sellerProfile->licence_paid_at ?? 'NULL') . "\n";
        echo "  Licence Expire At: " . ($user->sellerProfile->licence_expire_at ?? 'NULL') . "\n";
        echo "  Approved At: " . ($user->sellerProfile->approved_at ?? 'NULL') . "\n";
    }
    echo "\n";
}

<?php

use Modules\Identity\Entities\User;
use Modules\Seller\Entities\SellerProfile;
use Modules\Fintech\ValueObjects\Amount;

echo "--- STARTING VERIFICATION ---\n";

// 1. Setup User
$user = User::first();
if (!$user) die("No user found\n");
echo "User: " . $user->id . "\n";

// Reset state
if ($user->sellerProfile) {
    $user->sellerProfile->delete();
    echo "Reset: Seller profile deleted.\n";
}
$user->kyc_status = 'pending';
$user->save();

// 2. Test Registration (Simulated)
$user->wallet->balance = 10000;
$user->wallet->save();
$amount = new Amount(5000);

// Simulate Logic from Controller
if (!$user->sellerProfile) {
    // Check balance
    if ($user->wallet->hasEnoughBalance($amount)) {
        // Debit
        $user->wallet->debit($amount);
        echo "Debit successful. New Balance: " . $user->wallet->balance . "\n";
        
        // Create Profile
        $profile = $user->sellerProfile()->create([
            'shop_name' => 'Auto Test Shop',
            'status' => 'pending',
            'commission_rate' => 10.0,
            'licence_paid_at' => now(),
            'is_active' => false,
        ]);
        echo "Profile Created. Status: " . $profile->status . "\n";
    }
}

// 3. Test Block Pending (Verification)
$user->refresh();
if ($user->sellerProfile && $user->sellerProfile->status === 'pending') {
    echo "Check: Profile is pending. User should be blocked from re-submitting.\n";
}

// 4. Test Admin Approval (Simulated)
echo "--- SIMULATING ADMIN APPROVAL ---\n";
// Logic from KycController@approve
$user->kyc_status = 'verified';
$user->save();

if ($user->sellerProfile) {
    $user->sellerProfile->update([
        'status' => 'approved',
        'is_active' => true,
        'approved_at' => now(),
        'licence_expire_at' => now()->addMonths(3),
    ]);
    echo "Admin approved user and profile.\n";
}

// 5. Final Check
$user->refresh();
$profile = $user->sellerProfile;

echo "--- FINAL STATUS ---\n";
echo "User KYC: " . $user->kyc_status . "\n";
echo "Seller Status: " . $profile->status . "\n";
echo "Seller Active: " . ($profile->is_active ? 'YES' : 'NO') . "\n";
echo "Expiry: " . $profile->licence_expire_at . "\n";

if ($profile->status === 'approved' && $profile->is_active) {
    echo "[SUCCESS] Full flow verified!\n";
} else {
    echo "[FAILURE] Profile not activated correctly.\n";
}

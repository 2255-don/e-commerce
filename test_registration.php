<?php

use Modules\Identity\Entities\User;
use Modules\Seller\Entities\SellerProfile;
use Modules\Fintech\ValueObjects\Amount;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

// 1. Setup User
$user = User::first();
if (!$user) die("No user found\n");
echo "Testing with User ID: " . $user->id . "\n";

// Clear existing profile for test
if ($user->sellerProfile) {
    $user->sellerProfile->delete();
    echo "Cleared existing seller profile\n";
}

// 2. Test Insufficient Balance
$user->wallet->balance = 0;
$user->wallet->save();
echo "Set wallet balance to 0\n";

$wallet = $user->refresh()->wallet;
$amount = new Amount(5000);

if (!$wallet->hasEnoughBalance($amount)) {
    echo "[PASS] Detected insufficient balance correctly.\n";
} else {
    echo "[FAIL] Failed to detect insufficient balance.\n";
}

// 3. Test Successful Registration
$user->wallet->balance = 10000;
$user->wallet->save();
echo "Set wallet balance to 10000\n";

// Simulate Controller Logic
try {
    $wallet = $user->refresh()->wallet;
    
    // Debit
    $wallet->debit($amount);
    echo "Debited 5000. New Balance: " . $wallet->balance . "\n";
    
    // Create Profile
    $profile = $user->sellerProfile()->create([
        'shop_name' => 'Test Shop',
        'status' => 'pending',
        'commission_rate' => 10.0,
        'licence_paid_at' => now(),
    ]);
    
    echo "Created Seller Profile: " . $profile->id . "\n";
    echo "Status: " . $profile->status . "\n";
    
    if ($profile->status === 'pending') {
        echo "[PASS] Profile created with pending status.\n";
    } else {
        echo "[FAIL] Profile status is " . $profile->status . "\n";
    }

} catch (\Exception $e) {
    echo "[FAIL] Exception: " . $e->getMessage() . "\n";
}

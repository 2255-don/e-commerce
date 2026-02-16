<?php

use Modules\Seller\Entities\SellerProfile;
use Modules\Identity\Entities\User;
use Illuminate\Support\Facades\Schema;

echo "=== SCHEMA CHECK ===\n";

// Check if columns exist in seller_profiles table
$columns = Schema::getColumnListing('seller_profiles');
echo "Columns in seller_profiles:\n";
print_r($columns);

echo "\n=== FILLABLE CHECK ===\n";
$profile = new SellerProfile();
echo "Fillable attributes:\n";
print_r($profile->getFillable());

echo "\n=== TEST CREATION ===\n";
$user = User::first();
if (!$user) {
    die("No user found\n");
}

echo "User ID: {$user->id}\n";

try {
    // Test direct creation
    $testProfile = SellerProfile::create([
        'user_id' => $user->id,
        'shop_name' => 'Direct Test Shop',
        'status' => 'pending',
        'commission_rate' => 10.0,
        'is_active' => false,
    ]);
    
    echo "SUCCESS: Profile created with ID: {$testProfile->id}\n";
    echo "Shop Name: {$testProfile->shop_name}\n";
    echo "Status: {$testProfile->status}\n";
    
    // Clean up
    $testProfile->delete();
    echo "Test profile deleted.\n";
} catch (\Exception $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

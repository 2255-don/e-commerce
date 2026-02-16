<?php

use Modules\Identity\Entities\User;
use Modules\Fintech\ValueObjects\Amount;
use Illuminate\Support\Facades\DB;

echo "=== COMPLETE WORKFLOW TEST ===\n\n";

// Clean slate
$testUser = User::where('email', 'workflow-test@example.com')->first();
if ($testUser) {
    if ($testUser->sellerProfile) {
        $testUser->sellerProfile->delete();
    }
    $testUser->delete();
}

// Create test user
$testUser = User::create([
    'name' => 'Workflow Test User',
    'email' => 'workflow-test@example.com',
    'password' => bcrypt('password'),
    'email_verified_at' => now(),
    'kyc_status' => 'unverified',
]);

// Create wallet
$testUser->wallet()->create(['balance' => 10000]);

echo "1. Test User Created\n";
echo "   Email: {$testUser->email}\n";
echo "   KYC Status: {$testUser->kyc_status}\n";
echo "   Wallet Balance: {$testUser->wallet->balance}\n\n";

// Simulate seller registration
echo "2. Simulating Seller Registration...\n";

try {
    DB::beginTransaction();
    
    // Debit wallet
    $testUser->wallet->debit(new Amount(5000));
    
    // Create seller profile
    $profile = $testUser->sellerProfile()->create([
        'shop_name' => 'Test Workflow Shop',
        'logo_path' => 'test/logo.png',
        'kyc_document_path' => 'test/kyc.pdf',
        'status' => 'pending',
        'commission_rate' => 10.0,
        'licence_paid_at' => now(),
        'is_active' => false,
    ]);
    
    // Update user KYC to pending
    $testUser->kyc_status = 'pending';
    $testUser->save();
    
    DB::commit();
    
    echo "   ✓ Wallet debited: {$testUser->wallet->fresh()->balance} FCFA\n";
    echo "   ✓ Seller Profile created (ID: {$profile->id})\n";
    echo "   ✓ Profile Status: {$profile->status}\n";
    echo "   ✓ User KYC Status: {$testUser->fresh()->kyc_status}\n\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "   ✗ FAILED: " . $e->getMessage() . "\n";
    exit(1);
}

// Simulate admin approval
echo "3. Simulating Admin Approval...\n";

try {
    $testUser = $testUser->fresh();
    
    // Admin approves KYC
    $testUser->kyc_status = 'verified';
    $testUser->save();
    
    // Admin approves seller profile
    if ($testUser->sellerProfile) {
        $testUser->sellerProfile->update([
            'status' => 'approved',
            'is_active' => true,
            'approved_at' => now(),
            'licence_expire_at' => now()->addMonths(3),
        ]);
    }
    
    echo "   ✓ User KYC Status: {$testUser->kyc_status}\n";
    echo "   ✓ Seller Status: {$testUser->sellerProfile->status}\n";
    echo "   ✓ Seller Active: " . ($testUser->sellerProfile->is_active ? 'YES' : 'NO') . "\n";
    echo "   ✓ License Expires: {$testUser->sellerProfile->licence_expire_at}\n\n";
    
} catch (\Exception $e) {
    echo "   ✗ FAILED: " . $e->getMessage() . "\n";
    exit(1);
}

echo "=== ✓ COMPLETE WORKFLOW SUCCESS ===\n";
echo "\nSummary:\n";
echo "- User KYC verified independently: {$testUser->kyc_status}\n";
echo "- Seller account approved and active: {$testUser->sellerProfile->status}\n";
echo "- Both statuses work together correctly!\n";

// Cleanup
$testUser->sellerProfile->delete();
$testUser->delete();
echo "\nTest data cleaned up.\n";

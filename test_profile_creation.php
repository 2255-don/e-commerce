<?php

use Modules\Identity\Entities\User;
use Illuminate\Support\Facades\DB;

echo "=== SIMULATING SELLER REGISTRATION ===\n";

$user = User::first();
if (!$user) die("No user\n");

echo "User ID: {$user->id}\n";
echo "Testing profile creation...\n";

try {
    // Simulate the exact code from controller
    DB::beginTransaction();
    
    $profile = $user->sellerProfile()->create([
        'shop_name' => 'Test Shop Direct',
        'logo_path' => null,
        'kyc_document_path' => 'test/path.pdf',
        'status' => 'pending',
        'commission_rate' => 10.0,
        'licence_paid_at' => now(),
        'is_active' => false,
    ]);
    
    echo "SUCCESS: Profile created with ID: {$profile->id}\n";
    
    DB::rollBack(); // Don't actually save
    echo "Rolled back for testing.\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine(). "\n";
    echo "\nFull trace:\n";
    echo $e->getTraceAsString() . "\n";
}

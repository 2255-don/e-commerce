<?php

use Modules\Fintech\Services\WalletService;
use Modules\Identity\Entities\User;
use Illuminate\Support\Facades\Auth;

// Get first user
$user = User::first();
if (!$user) {
    echo "No user found.\n";
    exit;
}

echo "User ID: " . $user->id . "\n";

try {
    echo "Resolving WalletService...\n";
    $service = app(WalletService::class);
    echo "WalletService resolved.\n";

    echo "Calling getOrCreateWallet...\n";
    $wallet = $service->getOrCreateWallet($user->id);
    echo "Wallet: " . $wallet->id . " (Balance: " . $wallet->balance . ")\n";

} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

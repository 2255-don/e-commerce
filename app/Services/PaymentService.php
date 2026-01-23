<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class PaymentService
{
    /**
     * Handle payment from various sources.
     */
    public function processPayment(Wallet $senderWallet, float $amount, string $type, string $description)
    {
        return DB::transaction(function () use ($senderWallet, $amount, $type, $description) {
            if ($senderWallet->balance < $amount) {
                throw new Exception("Solde insuffisant dans le wallet.");
            }

            // Deduct balance
            $senderWallet->decrement('balance', $amount);

            // Create transaction record
            return Transaction::create([
                'sender_wallet_id' => $senderWallet->id,
                'type' => $type,
                'amount' => $amount,
                'reference' => 'PAY-' . strtoupper(Str::random(10)),
                'description' => $description,
                'status' => 'completed',
            ]);
        });
    }

    /**
     * Handle deposit into wallet (e.g., from Mobile Money or Stripe).
     */
    public function deposit(Wallet $receiverWallet, float $amount, string $reference, string $description)
    {
        return DB::transaction(function () use ($receiverWallet, $amount, $reference, $description) {
            $receiverWallet->increment('balance', $amount);

            return Transaction::create([
                'receiver_wallet_id' => $receiverWallet->id,
                'type' => 'deposit',
                'amount' => $amount,
                'reference' => $reference,
                'description' => $description,
                'status' => 'completed',
            ]);
        });
    }
}

<?php

namespace App\Services\Payment;

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
    /**
     * Transfer funds from one wallet to another.
     */
    public function transfer(Wallet $senderWallet, Wallet $receiverWallet, float $amount, string $description)
    {
        return DB::transaction(function () use ($senderWallet, $receiverWallet, $amount, $description) {
            if ($senderWallet->balance < $amount) {
                throw new Exception("Solde insuffisant pour le transfert.");
            }

            // Deduct from sender
            $senderWallet->decrement('balance', $amount);

            // Add to receiver
            $receiverWallet->increment('balance', $amount);

            // Create transaction record
            return Transaction::create([
                'sender_wallet_id' => $senderWallet->id,
                'receiver_wallet_id' => $receiverWallet->id,
                'type' => 'transfer',
                'amount' => $amount,
                'reference' => 'TRF-' . strtoupper(Str::random(10)),
                'description' => $description,
                'status' => 'completed',
            ]);
        });
    }
    /**
     * Handle withdrawal from wallet (to Mobile Money).
     */
    public function withdraw(Wallet $senderWallet, float $amount, string $phone, \App\Services\Payment\MobileMoneyService $mmService)
    {
        return DB::transaction(function () use ($senderWallet, $amount, $phone, $mmService) {
            
            // 1. Check Balance
            if ($senderWallet->balance < $amount) {
                throw new Exception("Solde insuffisant pour le retrait.");
            }

            // 2. Call Mobile Money Service (Simulate Transfer)
            // If this fails, exception is thrown and transaction rolls back
            $payoutResult = $mmService->processPayout($phone, $amount);

            // 3. Deduct from Wallet
            $senderWallet->decrement('balance', $amount);

            // 4. Record Transaction
            return Transaction::create([
                'sender_wallet_id' => $senderWallet->id,
                'type' => 'withdrawal',
                'amount' => $amount,
                'reference' => $payoutResult['reference'], // Use MM reference
                'description' => $payoutResult['message'],
                'status' => 'completed',
            ]);
        });
    }
}

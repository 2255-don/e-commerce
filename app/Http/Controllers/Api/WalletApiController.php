<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Services\PaymentService;
use App\Services\MobileMoneyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class WalletApiController extends Controller
{
    protected $paymentService;
    protected $mmService;

    public function __construct(PaymentService $paymentService, MobileMoneyService $mmService)
    {
        $this->paymentService = $paymentService;
        $this->mmService = $mmService;
    }

    /**
     * Get user wallet info.
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $wallet = $user->wallet ?? Wallet::create([
                'user_id' => $user->id,
                'balance' => 0,
                'currency' => 'XOF'
            ]);

            return response()->json([
                'balance' => $wallet->balance,
                'currency' => $wallet->currency,
                'transactions' => $wallet->transactionsAsSender()
                    ->union($wallet->transactionsAsReceiver())
                    ->latest()
                    ->take(10)
                    ->get()
            ]);
        } catch (Exception $e) {
            Log::error('API Wallet Error: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de la récupération du wallet.'], 500);
        }
    }

    /**
     * Process wallet recharge.
     */
    public function recharge(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'amount' => 'required|numeric|min:100',
        ]);

        try {
            $user = $request->user();
            $wallet = $user->wallet;

            $data = $this->mmService->processPayment($request->phone, $request->amount);

            $this->paymentService->deposit(
                $wallet,
                $request->amount,
                $data['transaction_id'],
                'Rechargement Wallet API'
            );

            return response()->json([
                'message' => 'Wallet rechargé avec succès.',
                'balance' => $wallet->fresh()->balance
            ]);
        } catch (Exception $e) {
            Log::error('API Recharge Error: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}

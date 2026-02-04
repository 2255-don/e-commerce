<?php

namespace App\Http\Controllers\mock;

use App\Http\Controllers\Controller;
use App\Services\Payment\MobileMoneyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class MockMobileMoneyController extends Controller
{
    protected $mmService;

    public function __construct(MobileMoneyService $mmService)
    {
        $this->mmService = $mmService;
    }

    /**
     * Simulate a Mobile Money payment via API.
     */
    public function pay(Request $request)
    {
        try {
            $validated = $request->validate([
                'phone' => 'required|string',
                'amount' => 'required|numeric|min:100',
                'provider' => 'nullable|string|in:airtel,vodacom,orange,africell',
                'otp' => 'nullable|string'
            ]);

            $result = $this->mmService->processPayment(
                $validated['phone'], 
                $validated['amount'],
                $validated['provider'] ?? null,
                $validated['otp'] ?? '1234'
            );
            
            return response()->json($result, 200);

        } catch (Exception $e) {
            $errorMsg = $e->getMessage();
            $code = 400;

            // Set appropriate HTTP status codes
            if (str_contains($errorMsg, 'Solde insuffisant')) $code = 402;
            if (str_contains($errorMsg, 'Erreur réseau')) $code = 503;
            if (str_contains($errorMsg, 'OTP incorrect')) $code = 401;
            if (str_contains($errorMsg, 'bloqué')) $code = 403;
            if (str_contains($errorMsg, 'Limite')) $code = 429;

            return response()->json([
                'status' => 'error',
                'message' => $errorMsg
            ], $code);
        }
    }
}

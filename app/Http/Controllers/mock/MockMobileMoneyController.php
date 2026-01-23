<?php

namespace App\Http\Controllers\mock;

use App\Http\Controllers\Controller;
use App\Services\MobileMoneyService;
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
            $request->validate([
                'phone' => 'required',
                'amount' => 'required|numeric',
            ]);

            $result = $this->mmService->processPayment($request->phone, $request->amount);
            
            return response()->json($result, 200);

        } catch (Exception $e) {
            $errorMsg = $e->getMessage();
            $code = 400;

            if (str_contains($errorMsg, 'Solde insuffisant')) $code = 402;
            if (str_contains($errorMsg, 'Erreur réseau')) $code = 503;

            return response()->json([
                'status' => 'error',
                'message' => $errorMsg
            ], $code);
        }
    }
}

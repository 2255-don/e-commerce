<?php

namespace App\Services\Payment;

use Exception;
use Illuminate\Support\Str;

class MobileMoneyService
{
    /**
     * Supported Mobile Money Providers in DRC
     */
    const PROVIDERS = [
        'airtel' => [
            'name' => 'Airtel Money',
            'prefixes' => ['097', '099'],
            'color' => 'danger',
            'fee_rate' => 0.015 // 1.5%
        ],
        'vodacom' => [
            'name' => 'Vodacom M-Pesa',
            'prefixes' => ['081', '082', '089'],
            'color' => 'danger',
            'fee_rate' => 0.015
        ],
        'orange' => [
            'name' => 'Orange Money',
            'prefixes' => ['084', '085'],
            'color' => 'warning',
            'fee_rate' => 0.02 // 2%
        ],
        'africell' => [
            'name' => 'Africell Money',
            'prefixes' => ['090'],
            'color' => 'primary',
            'fee_rate' => 0.015
        ]
    ];

    /**
     * Test scenarios based on phone last 4 digits
     * 
     * Magic Numbers Format: +243 [provider_prefix] XXX [scenario]
     * Examples:
     * +243 97 000 0000 = Airtel Success
     * +243 81 000 0001 = Vodacom Insufficient Balance
     * +243 84 000 0002 = Orange Network Error
     * +243 90 000 0003 = Africell Invalid OTP
     * +243 97 000 0004 = Airtel Daily Limit
     * +243 81 000 0005 = Vodacom Account Blocked
     * +243 84 000 0666 = Orange Slow Network (5s delay)
     */
    const TEST_SCENARIOS = [
        '0000' => 'success',
        '0001' => 'insufficient_balance',
        '0002' => 'network_error',
        '0003' => 'invalid_otp',
        '0004' => 'daily_limit',
        '0005' => 'account_blocked',
        '0666' => 'slow_network', // 5 seconds delay but success
    ];

    /**
     * Process a Mobile Money payment with realistic simulation.
     * 
     * @param string $phone Phone number (format: +243XXXXXXXXX or 0XXXXXXXXX)
     * @param float $amount Amount in FCFA
     * @param string|null $provider Provider code (auto-detected if null)
     * @param string|null $otp OTP code for validation (default: 1234 for success)
     * 
     * @return array Payment result with transaction details
     * @throws Exception On payment failure
     */
    public function processPayment(string $phone, float $amount, ?string $provider = null, ?string $otp = '1234'): array
    {
        // 1. Normalize phone number
        $phone = $this->normalizePhoneNumber($phone);

        // 2. Auto-detect provider if not provided
        if (!$provider) {
            $provider = $this->detectProvider($phone);
        }

        // 3. Validate provider exists
        if (!isset(self::PROVIDERS[$provider])) {
            throw new Exception("Opérateur mobile '{$provider}' non supporté");
        }

        // 4. Validate phone format for provider
        $this->validatePhoneNumber($phone, $provider);

        // 5. Get test scenario
        $scenario = $this->getTestScenario($phone);

        // 6. Simulate network delay (realistic)
        $this->simulateNetworkDelay($scenario);

        // 7. Validate OTP if scenario requires it
        if ($scenario === 'invalid_otp' && $otp !== '1234') {
            throw new Exception('Code OTP incorrect. Veuillez réessayer.');
        }

        // 8. Handle different scenarios
        return $this->handleScenario($scenario, $provider, $phone, $amount);
    }

    /**
     * Normalize phone number to standard format
     */
    private function normalizePhoneNumber(string $phone): string
    {
        // Remove spaces, dashes, parentheses
        $phone = preg_replace('/[\s\-\(\)]/', '', $phone);

        // Convert +243XXXXXXXXX to 0XXXXXXXXX
        if (str_starts_with($phone, '+243')) {
            $phone = '0' . substr($phone, 4);
        } elseif (str_starts_with($phone, '243')) {
            $phone = '0' . substr($phone, 3);
        }

        return $phone;
    }

    /**
     * Auto-detect provider from phone prefix
     */
    private function detectProvider(string $phone): string
    {
        $prefix = substr($phone, 0, 3);

        foreach (self::PROVIDERS as $code => $provider) {
            if (in_array($prefix, $provider['prefixes'])) {
                return $code;
            }
        }

        throw new Exception("Impossible de détecter l'opérateur pour le numéro {$phone}");
    }

    /**
     * Validate phone number format for specific provider
     */
    private function validatePhoneNumber(string $phone, string $provider): void
    {
        $validPrefixes = self::PROVIDERS[$provider]['prefixes'];
        $prefix = substr($phone, 0, 3);

        if (!in_array($prefix, $validPrefixes)) {
            $providerName = self::PROVIDERS[$provider]['name'];
            throw new Exception("Numéro invalide pour {$providerName}. Préfixes valides: " . implode(', ', $validPrefixes));
        }

        // Check length (DRC numbers: 10 digits starting with 0)
        if (strlen($phone) !== 10) {
            throw new Exception("Format de numéro invalide. Attendu: 10 chiffres (ex: 0971234567)");
        }
    }

    /**
     * Get test scenario based on phone last 4 digits
     */
    private function getTestScenario(string $phone): string
    {
        $lastFour = substr($phone, -4);

        return self::TEST_SCENARIOS[$lastFour] ?? 'success';
    }

    /**
     * Simulate realistic network delay
     */
    private function simulateNetworkDelay(string $scenario): void
    {
        // Delays in seconds
        $delays = [
            'success' => rand(1, 2),
            'slow_network' => 5,
            'network_error' => rand(2, 4),
            'insufficient_balance' => 1,
            'invalid_otp' => 1,
            'daily_limit' => 1,
            'account_blocked' => 1,
        ];

        $delay = $delays[$scenario] ?? 1;
        sleep($delay);
    }

    /**
     * Handle payment scenario and return appropriate response
     */
    private function handleScenario(string $scenario, string $provider, string $phone, float $amount): array
    {
        switch ($scenario) {
            case 'success':
            case 'slow_network':
                return $this->successResponse($provider, $phone, $amount);

            case 'insufficient_balance':
                throw new Exception('Solde insuffisant sur votre compte Mobile Money. Veuillez recharger et réessayer.');

            case 'network_error':
                throw new Exception('Erreur réseau. Impossible de contacter le serveur ' . self::PROVIDERS[$provider]['name'] . '. Veuillez réessayer plus tard.');

            case 'invalid_otp':
                throw new Exception('Code OTP incorrect ou expiré. Veuillez vérifier le code reçu par SMS.');

            case 'daily_limit':
                throw new Exception('Limite de transaction quotidienne atteinte. Maximum: 1,000,000 FCFA/jour.');

            case 'account_blocked':
                throw new Exception('Votre compte Mobile Money est temporairement bloqué. Contactez votre opérateur.');

            default:
                throw new Exception('Erreur inconnue lors du traitement du paiement.');
        }
    }

    /**
     * Generate success response with realistic details
     */
    private function successResponse(string $provider, string $phone, float $amount): array
    {
        $providerData = self::PROVIDERS[$provider];
        
        // Calculate operator fees
        $fees = round($amount * $providerData['fee_rate'], 2);
        $totalCharged = $amount + $fees;

        // Generate realistic transaction ID
        $transactionId = $this->generateTransactionId($provider);

        // Generate reference number
        $reference = 'REF-' . strtoupper(Str::random(10));

        // Simulate remaining balance (random for realism)
        $balanceAfter = rand(10000, 500000);

        return [
            'status' => 'success',
            'transaction_id' => $transactionId,
            'reference' => $reference,
            'provider' => $providerData['name'],
            'provider_code' => $provider,
            'phone' => substr($phone, 0, 3) . 'XXX' . substr($phone, -2), // Masked: 097XXX67
            'amount' => $amount,
            'fees' => $fees,
            'total_charged' => $totalCharged,
            'balance_after' => $balanceAfter,
            'timestamp' => now()->toIso8601String(),
            'message' => "Paiement de " . number_format($amount, 0, ',', ' ') . " FCFA effectué avec succès via {$providerData['name']}"
        ];
    }

    /**
     * Generate realistic transaction ID
     * Format: [PROVIDER]-[DATE]-[UNIQUE_HASH]
     * Example: AIR-20260202-A3B4C5D6
     */
    private function generateTransactionId(string $provider): string
    {
        $prefix = strtoupper(substr($provider, 0, 3)); // AIR, VOD, ORA, AFR
        $date = now()->format('Ymd'); // 20260202
        $hash = strtoupper(substr(md5(microtime() . rand()), 0, 8)); // A3B4C5D6

        return "{$prefix}-{$date}-{$hash}";
    }

    /**
     * Get available providers list
     */
    public static function getAvailableProviders(): array
    {
        return self::PROVIDERS;
    }

    /**
     * Get test numbers documentation
     */
    public static function getTestNumbers(): array
    {
        $testNumbers = [];
        
        foreach (self::PROVIDERS as $code => $provider) {
            $prefix = $provider['prefixes'][0]; // Use first prefix
            
            foreach (self::TEST_SCENARIOS as $digits => $scenario) {
                $phone = "+243 {$prefix} 000 {$digits}";
                $testNumbers[] = [
                    'phone' => $phone,
                    'provider' => $provider['name'],
                    'scenario' => $scenario,
                    'description' => self::getScenarioDescription($scenario)
                ];
            }
        }

        return $testNumbers;
    }

    /**
     * Get human-readable scenario description
     */
    private static function getScenarioDescription(string $scenario): string
    {
        return match($scenario) {
            'success' => '✅ Succès immédiat',
            'slow_network' => '🐌 Succès avec délai 5s (réseau lent)',
            'insufficient_balance' => '❌ Solde insuffisant',
            'network_error' => '⚠️ Erreur réseau',
            'invalid_otp' => '🔐 Code OTP incorrect',
            'daily_limit' => '📊 Limite quotidienne atteinte',
            'account_blocked' => '🔒 Compte bloqué',
            default => 'Scénario inconnu'
        };
    }
}

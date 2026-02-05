<?php

namespace Modules\Fintech\Services;

use Modules\Fintech\ValueObjects\TransactionReference;
use Exception;

/**
 * Payment Gateway Service
 * Gère l'intégration avec Mobile Money (mock pour le moment)
 */
class PaymentGatewayService
{
    private const PROVIDERS = ['airtel', 'vodacom', 'orange', 'africell'];
    
    /**
     * Initier un paiement Mobile Money
     */
    public function initiateMobileMoneyPayment(
        string $phoneNumber,
        float $amount,
        string $provider = 'airtel',
        ?string $otp = null
    ): array {
        // Validation
        if (!in_array(strtolower($provider), self::PROVIDERS)) {
            throw new Exception("Provider invalide: {$provider}");
        }
        
        if ($amount < 100) {
            throw new Exception('Montant minimum: 100 FCFA');
        }
        
        // Générer référence de transaction
        $transactionReference = TransactionReference::generate('MM')->getValue();
        
        // MOCK: Simulation Mobile Money
        // EN PRODUCTION: Appel API réelle du provider
        
        // Simulation: 10% de chance d'échec
        $success = rand(1, 100) > 10;
        
        if (!$success) {
            throw new Exception('Transaction Mobile Money échouée: solde insuffisant');
        }
        
        // Simulation delay réseau
        usleep(500000); // 0.5 secondes
        
        return [
            'success' => true,
            'transaction_id' => $transactionReference,
            'provider' => $provider,
            'amount' => $amount,
            'phone_number' => $phoneNumber,
            'status' => 'completed',
            'message' => 'Paiement Mobile Money réussi',
        ];
    }
    
    /**
     * Vérifier le statut d'un paiement
     */
    public function verifyPayment(string $transactionId): bool
    {
        // MOCK: En production, vérifier auprès du provider
        return true;
    }
    
    /**
     * Gérer callback du provider
     */
    public function handleCallback(array $payload): array
    {
        // MOCK: En production, valider signature et traiter callback
        return [
            'status' => 'processed',
            'transaction_id' => $payload['transaction_id'] ?? null,
        ];
    }
    
    /**
     * Calculer les frais par provider
     */
    public function calculateFees(float $amount, string $provider): float
    {
        // Frais par provider (exemple)
        $feeRates = [
            'airtel' => 0.02,    // 2%
            'vodacom' => 0.025,  // 2.5%
            'orange' => 0.02,    // 2%
            'africell' => 0.03,  // 3%
        ];
        
        $rate = $feeRates[strtolower($provider)] ?? 0.02;
        
        return $amount * $rate;
    }
}

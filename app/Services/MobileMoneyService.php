<?php

namespace App\Services;

use Exception;

class MobileMoneyService
{
    /**
     * Simulate a Mobile Money payment.
     * 
     * Numbers:
     * 80000000 -> Success
     * 80000001 -> Insufficient Balance
     * 80000002 -> Network Error
     * 
     * @throws Exception
     */
    public function processPayment(string $phone, float $amount): array
    {
        // On simule un délai réseau
        // usleep(500000); 

        switch ($phone) {
            case '80000000':
                return [
                    'status' => 'success',
                    'transaction_id' => 'MM-' . rand(100000, 999999),
                    'message' => 'Paiement effectué avec succès'
                ];

            case '80000001':
                throw new Exception('Solde insuffisant sur votre compte Mobile Money');

            case '80000002':
                throw new Exception('Erreur réseau, veuillez réessayer plus tard');

            default:
                throw new Exception('Numéro de téléphone non reconnu par le simulateur');
        }
    }
}

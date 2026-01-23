<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Exception;

class KycService
{
    /**
     * Handle the KYC document upload and user update.
     * 
     * @throws Exception
     */
    public function submitKyc(User $user, UploadedFile $file, string $shopName)
    {
        // 1. Détermine si l'utilisateur peut soumettre (Licence active)
        if (!$user->sellerProfile || !$user->sellerProfile->isLicenseActive()) {
            throw new Exception("Vous devez avoir une licence vendeur active pour soumettre un KYC.");
        }

        // 2. Vérifie qu'il n'y a pas déjà une demande en attente
        if ($user->kyc_status === 'pending') {
            throw new Exception("Une demande de validation est déjà en cours.");
        }

        // 3. Upload du fichier
        $path = $file->store('kyc_documents', 'public');

        if (!$path) {
            throw new Exception("Erreur lors de l'enregistrement du document.");
        }

        // 4. Mise à jour de l'utilisateur
        $user->kyc_status = 'pending';
        $user->kyc_document_path = $path;
        $user->save();

        // 5. Mise à jour du nom de la boutique dans le profil vendeur
        $user->sellerProfile->update([
            'shop_name' => $shopName
        ]);

        return $path;
    }

    /**
     * Update KYC status (Admin actions).
     */
    public function updateStatus(User $user, string $status)
    {
        if (!in_array($status, ['verified', 'rejected', 'pending'])) {
            throw new Exception("Statut invalide.");
        }

        $user->kyc_status = $status;
        $user->save();

        return $user;
    }
}

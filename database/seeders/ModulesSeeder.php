<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;
use Modules\Identity\Entities\Module as EntitiesModule;

class ModulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            [
                'slug' => 'auth',
                'name' => 'Authentification',
                'icon' => 'bx-lock',
                'color' => '#B8860B',
                'order' => 1,
                'is_core' => true,
            ],
            [
                'slug' => 'users',
                'name' => 'Gestion Utilisateurs',
                'icon' => 'bx-user',
                'color' => '#D4AF37',
                'order' => 2,
                'is_core' => false,
            ],
            [
                'slug' => 'profils',
                'name' => 'Profils & Rôles',
                'icon' => 'bx-shield',
                'color' => '#808080',
                'order' => 3,
                'is_core' => false,
            ],
            [
                'slug' => 'wallet',
                'name' => 'Portefeuille',
                'icon' => 'bx-wallet',
                'color' => '#B8860B',
                'order' => 4,
                'is_core' => false,
            ],
            [
                'slug' => 'kyc',
                'name' => 'Vérification KYC',
                'icon' => 'bx-id-card',
                'color' => '#505050',
                'order' => 5,
                'is_core' => false,
            ],
            [
                'slug' => 'products',
                'name' => 'Produits',
                'icon' => 'bx-package',
                'color' => '#D4AF37',
                'order' => 6,
                'is_core' => false,
            ],
            [
                'slug' => 'orders',
                'name' => 'Commandes',
                'icon' => 'bx-cart',
                'color' => '#B8860B',
                'order' => 7,
                'is_core' => false,
            ],
            [
                'slug' => 'seller',
                'name' => 'Espace Vendeur',
                'icon' => 'bx-store',
                'color' => '#808080',
                'order' => 8,
                'is_core' => false,
            ],
        ];

        foreach ($modules as $moduleData) {
            EntitiesModule::updateOrCreate(
                ['slug' => $moduleData['slug']],
                $moduleData
            );
        }

        $this->command->info('8 modules created/updated successfully!');
    }
}

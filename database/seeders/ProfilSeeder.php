<?php

namespace Database\Seeders;

use App\Models\Profil;
use Illuminate\Database\Seeder;

class ProfilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $profils = [
            ['libelle' => 'Super-Admin'],
            ['libelle' => 'Agent de support'],
            ['libelle' => 'Verificateur kyc'],
            ['libelle' => 'utilisateur'],
            ['libelle' => 'Admin-entreprise'],
        ];

        foreach ($profils as $profil) {
            Profil::firstOrCreate($profil);
        }
    }
}

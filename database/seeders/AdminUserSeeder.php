<?php

namespace Database\Seeders;

use Modules\Identity\Entities\User;
use Modules\Identity\Entities\Profil;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the Super-Admin profile
        $superAdminProfil = Profil::where('libelle', 'Super-Admin')->first();

        User::updateOrCreate(
            ['email' => 'traorevetio22@gmail.com'],
            [
                'name' => 'Don Manuel',
                'password' => Hash::make('12345678'),
                'profil_id' => $superAdminProfil->id,
                'email_verified_at' => now(),
            ]
        );
    }
}

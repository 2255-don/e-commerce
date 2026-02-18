<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

class PlatformWalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::where('email', 'traorevetio22@gmail.com')->first();

        if ($superAdmin) {
            if (!$superAdmin->wallet) {
                Wallet::create([
                    'user_id' => $superAdmin->id,
                    'balance' => 0,
                    'currency' => 'XAF', // Assuming CFA Francs
                    'status' => 'active', 
                ]);
                $this->command->info('Platform Wallet created successfully.');
            } else {
                $this->command->info('Platform Wallet already exists.');
            }
        } else {
            $this->command->error('Super Admin user not found. Please run AdminUserSeeder first.');
        }
    }
}

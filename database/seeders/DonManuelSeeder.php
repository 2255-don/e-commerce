<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profil;
use App\Models\SellerProfile;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DonManuelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Get the 'utilisateur' profile
        $userProfil = Profil::firstOrCreate(['libelle' => 'utilisateur']);

        // 2. Create User
        $user = User::updateOrCreate(
            ['email' => 'traorejouanelle22@gmail.com'],
            [
                'name' => 'Don Manuel',
                'password' => Hash::make('12345678'),
                'profil_id' => $userProfil->id,
                'email_verified_at' => now(),
                'kyc_status' => 'verified',
                // Using an existing file from storage as identified
                'kyc_document_path' => 'seller/kyc/0eDQWdVbCDmNUxGVyTP3zA95bNMXxCPqI0cPwcK6.png',
            ]
        );

        // 3. Create Seller Profile
        $sellerProfile = SellerProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'shop_name' => 'Don Manuel Shop',
                'licence_paid_at' => now(),
                'licence_expire_at' => now()->addYear(),
                'commission_rate' => 1.00, // Default 1% commission
                'is_active' => true,
            ]
        );

        // 4. Link existing products or create new ones if none exist
        $productsCount = Product::count();
        $targetCount = 2;
        
        if ($productsCount < $targetCount) {
            $needed = $targetCount - $productsCount;
            $this->command->info("Creating {$needed} new products for Don Manuel...");
            
            $category = \App\Models\Category::inRandomOrder()->first();
            if (!$category) {
                 // Fallback if no categories exist (should not happen if CategorySeeder runs first)
                 $category = \App\Models\Category::create([
                     'name' => 'Default Category',
                     'slug' => 'default-category',
                     'id' => '999999' // Legacy ID as string
                 ]);
            }

            for ($i = 0; $i < $needed; $i++) {
                Product::create([
                    'seller_id' => $sellerProfile->id,
                    'category_id' => $category->id,
                    'title' => 'Produit Exemple ' . ($i + 1),
                    'description' => 'Description du produit exemple pour Don Manuel.',
                    'price' => rand(100, 10000),
                    'stock_quantity' => rand(5, 50),
                    'type' => 'physical', // Assuming 'physical' is a valid type or nullable
                ]);
            }
        }
        
        // Link all (or specifically 2) products to Don Manuel
        // The requirement said "link him to the two existing products", implying ownership transfer.
        $products = Product::where('seller_id', '!=', $sellerProfile->id)->take(2)->get();
        if ($products->count() > 0) {
             foreach ($products as $product) {
                $product->seller_id = $sellerProfile->id;
                $product->save();
            }
             $this->command->info("Transferred ownership of {$products->count()} existing products to Don Manuel.");
        }
        
        // Also ensure the newly created ones are his (they are created with his ID)
        $totalProducts = Product::where('seller_id', $sellerProfile->id)->count();
        
        $this->command->info("User \"Don Manuel\" now has {$totalProducts} products.");
    }
}

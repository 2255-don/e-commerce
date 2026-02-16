<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Modules\Seller\Entities\SellerProfile;
use Modules\Marketplace\Entities\Category;
use Modules\Marketplace\Entities\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Get Profil Utilisateur
        $profilUtilisateur = \Modules\Identity\Entities\Profil::where('libelle', 'utilisateur')->first()->id;

        // 1. Create or Get Seller User
        $sellerUser = User::firstOrCreate(
            ['email' => 'seller@example.com'],
            [
                'name' => 'Demo Seller',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'profil_id' => $profilUtilisateur,
            ]
        );

        // 2. Create Seller Profile
        $sellerProfile = SellerProfile::firstOrCreate(
            ['user_id' => $sellerUser->id],
            [
                'shop_name' => 'Tech Store Official',
                'business_name' => 'Tech Store LLC',
                'license_number' => 'TXT-123456789',
                'commission_rate' => 5.00,
                'status' => 'approved',
                'is_active' => true,
                'approved_at' => now(),
            ]
        );

        // 3. Get Category
        $category = Category::where('slug', 'smartphones-android')->first();
        if (!$category) {
            // Fallback if category seeding failed or slug changed
            $category = Category::create([
                'name' => 'Smartphones Android',
                'slug' => 'smartphones-android',
            ]);
        }

        // 4. Create Products
        $products = [
            [
                'title' => 'Samsung Galaxy S24 Ultra',
                'description' => 'The latest Samsung flagship with AI features.',
                'price' => 1299.99,
                'stock_quantity' => 50,
                'sku' => 'SAMSUNG-S24U-512',
            ],
            [
                'title' => 'Google Pixel 8 Pro',
                'description' => 'Pro-level camera and Google AI.',
                'price' => 999.00,
                'stock_quantity' => 30,
                'sku' => 'GOOGLE-P8P-128',
            ],
        ];

        foreach ($products as $data) {
            Product::firstOrCreate(
                ['sku' => $data['sku']],
                [
                    'seller_id' => $sellerProfile->user_id,
                    'category_id' => $category->id,
                    'title' => $data['title'],
                    'slug' => Str::slug($data['title']),
                    'description' => $data['description'],
                    'price' => $data['price'],
                    'stock_quantity' => $data['stock_quantity'],
                    'type' => 'physical_good',
                    'is_active' => true,
                ]
            );
        }
    }
}

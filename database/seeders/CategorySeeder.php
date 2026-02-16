<?php

namespace Database\Seeders;

use Modules\Marketplace\Entities\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Données des catégories parentes avec leur ID "legacy" explicit (string)
        $parentsData = [
            '1' => ['name' => 'Électronique & Téléphones', 'slug' => 'electronique-telephones', 'icon_path' => 'assets/icons/phone.png'],
            '2' => ['name' => 'Informatique & Bureau', 'slug' => 'informatique-bureau', 'icon_path' => 'assets/icons/laptop.png'],
            '3' => ['name' => 'Mode Homme', 'slug' => 'mode-homme', 'icon_path' => 'assets/icons/shirt.png'],
            '4' => ['name' => 'Mode Femme', 'slug' => 'mode-femme', 'icon_path' => 'assets/icons/dress.png'],
            '5' => ['name' => 'Santé & Beauté', 'slug' => 'sante-beaute', 'icon_path' => 'assets/icons/makeup.png'],
            '6' => ['name' => 'Maison & Électroménager', 'slug' => 'maison-electromenager', 'icon_path' => 'assets/icons/fridge.png'],
            '7' => ['name' => 'Alimentation & Supermarché', 'slug' => 'alimentation', 'icon_path' => 'assets/icons/food.png'],
            '8' => ['name' => 'Véhicules & Pièces', 'slug' => 'vehicules', 'icon_path' => 'assets/icons/car.png'],
            '9' => ['name' => 'Immobilier', 'slug' => 'immobilier', 'icon_path' => 'assets/icons/house.png'],
            '10' => ['name' => 'Services & Prestations', 'slug' => 'services', 'icon_path' => 'assets/icons/handshake.png'],
            '11' => ['name' => 'Agro-Business & Élevage', 'slug' => 'agro-business', 'icon_path' => 'assets/icons/cow.png'],
            '12' => ['name' => 'Divers', 'slug' => 'divers', 'icon_path' => 'assets/icons/box.png'],
        ];

        // Mapping pour stocker les IDs finaux
        $createdParents = [];

        foreach ($parentsData as $legacyId => $data) {
            // On cherche par slug pour éviter les doublons si déjà existant
            $category = Category::firstOrCreate(
                ['slug' => $data['slug']],
                [
                    'id' => $legacyId, // Force l'ID legacy si création
                    'name' => $data['name'],
                    'icon_path' => $data['icon_path']
                ]
            );
            $createdParents[$legacyId] = $category->id;
        }

        // Données des sous-catégories
        $subCategoriesData = [
            ['name' => 'Smartphones Android', 'slug' => 'smartphones-android', 'parent_legacy_id' => '1'],
            ['name' => 'Ordinateurs Portables', 'slug' => 'laptops', 'parent_legacy_id' => '2'],
            ['name' => 'Coiffure & Soins', 'slug' => 'coiffure', 'parent_legacy_id' => '5'],
            ['name' => 'Location Appartement', 'slug' => 'location-appart', 'parent_legacy_id' => '9'],
        ];

        foreach ($subCategoriesData as $subData) {
            $parentId = $createdParents[$subData['parent_legacy_id']] ?? null;

            if ($parentId) {
                Category::firstOrCreate(
                    ['slug' => $subData['slug']],
                    [
                        'name' => $subData['name'],
                        'parent_id' => $parentId,
                    ]
                );
            }
        }
    }
}

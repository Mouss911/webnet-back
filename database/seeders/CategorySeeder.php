<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Électronique',
                'description' => 'Appareils électroniques et accessoires'
            ],
            [
                'name' => 'Vêtements',
                'description' => 'Mode et habillement'
            ],
            [
                'name' => 'Livres',
                'description' => 'Livres et publications'
            ],
            [
                'name' => 'Maison & Jardin',
                'description' => 'Décoration d\'intérieur et articles de jardinage'
            ],
            [
                'name' => 'Sport & Loisirs',
                'description' => 'Équipements sportifs et articles de loisirs'
            ],
            [
                'name' => 'Beauté & Santé',
                'description' => 'Produits de beauté et de bien-être'
            ],
            [
                'name' => 'Informatique',
                'description' => 'Ordinateurs et accessoires informatiques'
            ],
            [
                'name' => 'Alimentation',
                'description' => 'Produits alimentaires et boissons'
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Smartphone XYZ',
                'description' => 'Latest model smartphone with advanced features',
                'price' => 699.99,
                'stock' => 50,
                'category_id' => 1,
                'image' => 'smartphone.jpg'
            ],
            [
                'name' => 'Cotton T-Shirt',
                'description' => 'Comfortable cotton t-shirt',
                'price' => 19.99,
                'stock' => 100,
                'category_id' => 2,
                'image' => 'tshirt.jpg'
            ],
            [
                'name' => 'Programming Book',
                'description' => 'Learn programming from scratch',
                'price' => 45.99,
                'stock' => 30,
                'category_id' => 3,
                'image' => 'book.jpg'
            ],
            [
                'name' => 'Garden Tools Set',
                'description' => 'Complete set of essential garden tools',
                'price' => 89.99,
                'stock' => 20,
                'category_id' => 4,
                'image' => 'tools.jpg'
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}

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
                'name' => 'Electronics',
                'description' => 'Electronic devices and accessories'
            ],
            [
                'name' => 'Clothing',
                'description' => 'Fashion and apparel'
            ],
            [
                'name' => 'Books',
                'description' => 'Books and publications'
            ],
            [
                'name' => 'Home & Garden',
                'description' => 'Home decor and gardening items'
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}

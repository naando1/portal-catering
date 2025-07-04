<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Makanan Utama',
                'description' => 'Menu makanan utama untuk makan siang atau makan malam',
                'image' => 'categories/main-food.jpg'
            ],
            [
                'name' => 'Diet Khusus',
                'description' => 'Menu untuk diet khusus seperti rendah kalori, rendah gula, dll',
                'image' => 'categories/diet.jpg'
            ],
            [
                'name' => 'Vegetarian',
                'description' => 'Menu khusus vegetarian dan vegan',
                'image' => 'categories/vegetarian.jpg'
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
        
        $this->command->info('Categories seeded successfully!');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Category;
use App\Models\CateringPartner;

class SampleMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan kategori ada
        $categories = Category::all();
        if ($categories->isEmpty()) {
            $this->command->error('Kategori tidak ditemukan. Jalankan CategorySeeder terlebih dahulu.');
            return;
        }

        // Pastikan partner catering ada
        $partners = CateringPartner::all();
        if ($partners->isEmpty()) {
            $this->command->error('Partner catering tidak ditemukan.');
            return;
        }

        // Daftar menu diet
        $menus = [
            [
                'name' => 'Nasi Merah dengan Ayam Panggang',
                'description' => 'Nasi merah organik dengan ayam panggang bumbu rempah rendah garam. Cocok untuk diet sehat.',
                'price' => 35000,
                'image' => 'menu/nasi-merah-ayam-panggang.jpg',
                'calories' => 450,
                'proteins' => 28,
                'carbs' => 65,
                'fats' => 8,
                'sugars' => 2,
                'sodium' => 320,
                'fiber' => 5,
                'cooking_method' => 'panggang',
                'carbohydrate_type' => 'kompleks',
                'is_available' => true,
                'is_diet_menu' => true,
                'category_id' => $categories->where('name', 'Makanan Utama')->first()->id ?? $categories->first()->id,
                'catering_partner_id' => $partners->random()->id,
                'ingredient_tags' => json_encode(['daging']),
                'taste_tags' => json_encode(['gurih']),
            ],
            [
                'name' => 'Salad Tuna Mediterania',
                'description' => 'Salad segar dengan tuna panggang, selada, tomat, mentimun, zaitun, dan saus lemon olive oil.',
                'price' => 42000,
                'image' => 'menu/salad-tuna.jpg',
                'calories' => 320,
                'proteins' => 25,
                'carbs' => 15,
                'fats' => 18,
                'sugars' => 4,
                'sodium' => 280,
                'fiber' => 6,
                'cooking_method' => 'panggang',
                'carbohydrate_type' => 'kompleks',
                'is_available' => true,
                'is_diet_menu' => true,
                'category_id' => $categories->where('name', 'Salad')->first()->id ?? $categories->first()->id,
                'catering_partner_id' => $partners->random()->id,
                'ingredient_tags' => json_encode(['ikan']),
                'taste_tags' => json_encode(['gurih', 'asam']),
            ],
            [
                'name' => 'Oatmeal dengan Buah Segar',
                'description' => 'Oatmeal dengan campuran buah segar, kacang almond, dan madu. Sarapan sehat kaya serat.',
                'price' => 28000,
                'image' => 'menu/oatmeal-buah.jpg',
                'calories' => 280,
                'proteins' => 10,
                'carbs' => 45,
                'fats' => 7,
                'sugars' => 12,
                'sodium' => 50,
                'fiber' => 8,
                'cooking_method' => 'rebus',
                'carbohydrate_type' => 'kompleks',
                'is_available' => true,
                'is_diet_menu' => true,
                'category_id' => $categories->where('name', 'Sarapan')->first()->id ?? $categories->first()->id,
                'catering_partner_id' => $partners->random()->id,
                'ingredient_tags' => json_encode(['kacang']),
                'taste_tags' => json_encode(['manis']),
            ],
            [
                'name' => 'Sup Sayur dengan Tahu',
                'description' => 'Sup sayuran segar dengan tahu, wortel, brokoli, dan jamur. Rendah kalori dan kaya serat.',
                'price' => 25000,
                'image' => 'menu/sup-sayur-tahu.jpg',
                'calories' => 180,
                'proteins' => 12,
                'carbs' => 20,
                'fats' => 5,
                'sugars' => 6,
                'sodium' => 320,
                'fiber' => 7,
                'cooking_method' => 'rebus',
                'carbohydrate_type' => 'kompleks',
                'is_available' => true,
                'is_diet_menu' => true,
                'category_id' => $categories->where('name', 'Sup')->first()->id ?? $categories->first()->id,
                'catering_partner_id' => $partners->random()->id,
                'ingredient_tags' => json_encode([]),
                'taste_tags' => json_encode(['gurih']),
            ],
            [
                'name' => 'Nasi Goreng Merah Rendah Lemak',
                'description' => 'Nasi goreng dengan bumbu khusus rendah lemak, telur orak-arik, dan sayuran.',
                'price' => 32000,
                'image' => 'menu/nasi-goreng-merah.jpg',
                'calories' => 380,
                'proteins' => 15,
                'carbs' => 60,
                'fats' => 9,
                'sugars' => 3,
                'sodium' => 450,
                'fiber' => 4,
                'cooking_method' => 'goreng',
                'carbohydrate_type' => 'kompleks',
                'is_available' => true,
                'is_diet_menu' => true,
                'category_id' => $categories->where('name', 'Makanan Utama')->first()->id ?? $categories->first()->id,
                'catering_partner_id' => $partners->random()->id,
                'ingredient_tags' => json_encode(['telur']),
                'taste_tags' => json_encode(['gurih', 'pedas']),
            ],
            // Tambahkan menu lain sesuai kebutuhan
        ];

        foreach ($menus as $menuData) {
            $ingredientTags = $menuData['ingredient_tags'] ?? [];
            $tasteTags = $menuData['taste_tags'] ?? [];
            
            unset($menuData['ingredient_tags']);
            unset($menuData['taste_tags']);
            
            $menuData['ingredient_tags'] = json_encode($ingredientTags);
            $menuData['taste_tags'] = json_encode($tasteTags);
            
            Menu::create($menuData);
        }

        $this->command->info('Sample menus created successfully!');
    }
}

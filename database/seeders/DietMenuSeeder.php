<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Category;
use App\Models\CateringPartner;
use App\Models\DietTag;
use App\Services\DietRecommendationService;

class DietMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Dapatkan kategori Diet Khusus
        $dietCategory = Category::where('name', 'Diet Khusus')->first();
        if (!$dietCategory) {
            $this->command->error('Diet category not found! Make sure to run CategorySeeder first.');
            return;
        }
        
        // Dapatkan mitra katering
        $partners = CateringPartner::all();
        if ($partners->isEmpty()) {
            $this->command->error('No catering partners found! Make sure to run CateringPartnerSeeder first.');
            return;
        }
        
        // Dapatkan semua diet tags
        $dietTags = DietTag::all()->keyBy('slug');
        
        // Menu diet
        $dietMenus = [
            [
                'name' => 'Salad Ayam Panggang',
                'description' => 'Salad dengan ayam panggang tanpa kulit, sayuran segar, dan dressing rendah lemak',
                'price' => 45000,
                'image' => 'menus/salad-ayam.jpg',
                'calories' => 350,
                'proteins' => 25,
                'carbohydrates' => 30,
                'fats' => 8,
                'sugars' => 5,
                'sodium' => 400,
                'fiber' => 6,
                'cooking_method' => 'panggang',
                'carbohydrate_type' => 'kompleks',
                'is_available' => true,
                'is_diet_menu' => true,
                'ingredient_tags' => json_encode(['daging']),
                'taste_tags' => json_encode(['gurih']),
                'catering_partner_id' => $partners->random()->id,
                'category_id' => $dietCategory->id,
            ],
            [
                'name' => 'Bowl Quinoa Sayur',
                'description' => 'Bowl quinoa dengan campuran sayuran, kacang-kacangan, dan dressing lemon olive oil',
                'price' => 50000,
                'image' => 'menus/quinoa-bowl.jpg',
                'calories' => 420,
                'proteins' => 15,
                'carbohydrates' => 60,
                'fats' => 12,
                'sugars' => 8,
                'sodium' => 350,
                'fiber' => 8,
                'cooking_method' => 'rebus',
                'carbohydrate_type' => 'kompleks',
                'is_available' => true,
                'is_diet_menu' => true,
                'ingredient_tags' => json_encode(['kacang']),
                'taste_tags' => json_encode(['gurih', 'asam']),
                'catering_partner_id' => $partners->random()->id,
                'category_id' => $dietCategory->id,
            ],
            [
                'name' => 'Ikan Kukus Bumbu Kuning',
                'description' => 'Ikan kukus dengan bumbu kuning rendah garam, sayuran, dan nasi merah',
                'price' => 55000,
                'image' => 'menus/ikan-kukus.jpg',
                'calories' => 380,
                'proteins' => 28,
                'carbohydrates' => 45,
                'fats' => 6,
                'sugars' => 3,
                'sodium' => 300,
                'fiber' => 5,
                'cooking_method' => 'kukus',
                'carbohydrate_type' => 'kompleks',
                'is_available' => true,
                'is_diet_menu' => true,
                'ingredient_tags' => json_encode([]),
                'taste_tags' => json_encode(['gurih']),
                'catering_partner_id' => $partners->random()->id,
                'category_id' => $dietCategory->id,
            ],
            [
                'name' => 'Sup Sayur Tinggi Serat',
                'description' => 'Sup sayuran dengan berbagai macam sayuran tinggi serat dan protein nabati',
                'price' => 40000,
                'image' => 'menus/sup-sayur.jpg',
                'calories' => 280,
                'proteins' => 12,
                'carbohydrates' => 40,
                'fats' => 5,
                'sugars' => 6,
                'sodium' => 350,
                'fiber' => 10,
                'cooking_method' => 'rebus',
                'carbohydrate_type' => 'kompleks',
                'is_available' => true,
                'is_diet_menu' => true,
                'ingredient_tags' => json_encode([]),
                'taste_tags' => json_encode(['gurih']),
                'catering_partner_id' => $partners->random()->id,
                'category_id' => $dietCategory->id,
            ],
            [
                'name' => 'Oatmeal Buah Rendah Gula',
                'description' => 'Oatmeal dengan campuran buah segar tanpa gula tambahan',
                'price' => 35000,
                'image' => 'menus/oatmeal.jpg',
                'calories' => 320,
                'proteins' => 10,
                'carbohydrates' => 55,
                'fats' => 7,
                'sugars' => 10,
                'sodium' => 100,
                'fiber' => 8,
                'cooking_method' => 'rebus',
                'carbohydrate_type' => 'kompleks',
                'is_available' => true,
                'is_diet_menu' => true,
                'ingredient_tags' => json_encode([]),
                'taste_tags' => json_encode(['manis']),
                'catering_partner_id' => $partners->random()->id,
                'category_id' => $dietCategory->id,
            ],
        ];
        
        foreach ($dietMenus as $menuData) {
            // Buat atau update menu
            $menu = Menu::updateOrCreate(
                ['name' => $menuData['name'], 'catering_partner_id' => $menuData['catering_partner_id']],
                $menuData
            );
            
            // Tentukan diet tags berdasarkan aturan yang sudah ada di aplikasi
            $applicableTags = $this->determineApplicableDietTags($menu, $dietTags);
            
            // Attach diet tags
            if (!empty($applicableTags)) {
                $menu->dietTags()->sync($applicableTags);
            }
        }
        
        $this->command->info('Diet menus seeded successfully!');
    }
    
    /**
     * Menentukan diet tags yang sesuai berdasarkan aturan yang sudah ada di aplikasi
     * Menggunakan aturan yang sama seperti di HealthProfile::getDietaryRestrictions()
     * dan DietRecommendationService::getRecommendedMenus()
     */
    private function determineApplicableDietTags($menu, $dietTags)
    {
        $applicableTags = [];
        
        // Aturan untuk Diabetes-Friendly
        if ($menu->sugars <= 10 && 
            $menu->carbohydrate_type === 'kompleks' && 
            $menu->fats <= 10) {
            if (isset($dietTags['diabetes-friendly'])) {
                $applicableTags[] = $dietTags['diabetes-friendly']->id;
            }
        }
        
        // Aturan untuk Hipertensi-Friendly
        if ($menu->sodium <= 600) {
            $tasteTags = json_decode($menu->taste_tags ?? '[]', true);
            if (!in_array('saus_asin', $tasteTags)) {
                if (isset($dietTags['hipertensi-friendly'])) {
                    $applicableTags[] = $dietTags['hipertensi-friendly']->id;
                }
            }
        }
        
        // Aturan untuk Jantung-Friendly
        if ($menu->fats <= 10 && 
            $menu->sodium <= 600 && 
            $menu->sugars <= 10 && 
            $menu->cooking_method !== 'goreng') {
            
            $ingredientTags = json_decode($menu->ingredient_tags ?? '[]', true);
            if (!in_array('jeroan', $ingredientTags) && 
                !in_array('kuning_telur', $ingredientTags)) {
                if (isset($dietTags['jantung-friendly'])) {
                    $applicableTags[] = $dietTags['jantung-friendly']->id;
                }
            }
        }
        
        // Aturan untuk Kolesterol-Friendly
        if ($menu->fats <= 10 && $menu->cooking_method !== 'goreng') {
            $ingredientTags = json_decode($menu->ingredient_tags ?? '[]', true);
            if (!in_array('jeroan', $ingredientTags) && 
                !in_array('kulit_ayam', $ingredientTags)) {
                if (isset($dietTags['kolesterol-friendly'])) {
                    $applicableTags[] = $dietTags['kolesterol-friendly']->id;
                }
            }
        }
        
        // Aturan untuk Ambeien-Friendly
        if ($menu->fiber >= 3 && $menu->cooking_method !== 'goreng') {
            $tasteTags = json_decode($menu->taste_tags ?? '[]', true);
            if (!in_array('pedas', $tasteTags)) {
                if (isset($dietTags['ambeien-friendly'])) {
                    $applicableTags[] = $dietTags['ambeien-friendly']->id;
                }
            }
        }
        
        // Aturan untuk tag nutrisi umum
        
        // Rendah Gula
        if ($menu->sugars <= 10) {
            if (isset($dietTags['rendah-gula'])) {
                $applicableTags[] = $dietTags['rendah-gula']->id;
            }
        }
        
        // Rendah Sodium
        if ($menu->sodium <= 600) {
            if (isset($dietTags['rendah-sodium'])) {
                $applicableTags[] = $dietTags['rendah-sodium']->id;
            }
        }
        
        // Rendah Lemak
        if ($menu->fats <= 10) {
            if (isset($dietTags['rendah-lemak'])) {
                $applicableTags[] = $dietTags['rendah-lemak']->id;
            }
        }
        
        // Tinggi Serat
        if ($menu->fiber >= 3) {
            if (isset($dietTags['tinggi-serat'])) {
                $applicableTags[] = $dietTags['tinggi-serat']->id;
            }
        }
        
        // Karbohidrat Kompleks
        if ($menu->carbohydrate_type === 'kompleks') {
            if (isset($dietTags['karbo-kompleks'])) {
                $applicableTags[] = $dietTags['karbo-kompleks']->id;
            }
        }
        
        return $applicableTags;
    }
}

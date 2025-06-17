<?php
// database/seeders/DietTagSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DietTag;

class DietTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            [
                'name' => 'Low Fat',
                'description' => 'Menu dengan kandungan lemak rendah, cocok untuk diet rendah lemak atau jantung.',
            ],
            [
                'name' => 'Low Sugar',
                'description' => 'Menu dengan kandungan gula rendah, cocok untuk diabetes atau diet gula.',
            ],
            [
                'name' => 'Low Sodium',
                'description' => 'Menu dengan kandungan garam rendah, cocok untuk hipertensi atau penyakit jantung.',
            ],
            [
                'name' => 'High Fiber',
                'description' => 'Menu dengan serat tinggi, cocok untuk pencernaan dan diet seimbang.',
            ],
            [
                'name' => 'Gluten Free',
                'description' => 'Menu tanpa gluten, cocok untuk penderita celiac atau sensitivitas gluten.',
            ],
            [
                'name' => 'Dairy Free',
                'description' => 'Menu tanpa produk susu, cocok untuk intoleransi laktosa atau alergi susu.',
            ],
            [
                'name' => 'Vegetarian',
                'description' => 'Menu tanpa daging, tetapi mungkin mengandung produk hewani lain seperti telur atau susu.',
            ],
            [
                'name' => 'Vegan',
                'description' => 'Menu tanpa produk hewani apapun, termasuk daging, telur, susu, dll.',
            ],
            [
                'name' => 'Heart Healthy',
                'description' => 'Menu yang dirancang untuk kesehatan jantung, rendah lemak jenuh dan sodium.',
            ],
            [
                'name' => 'Diabetic Friendly',
                'description' => 'Menu yang cocok untuk penderita diabetes, dengan gula dan karbohidrat terkontrol.',
            ],
            [
                'name' => 'Contains Nuts',
                'description' => 'Menu yang mengandung kacang-kacangan, perhatian bagi yang alergi kacang.',
            ],
            [
                'name' => 'Contains Seafood',
                'description' => 'Menu yang mengandung makanan laut, perhatian bagi yang alergi seafood.',
            ],
            [
                'name' => 'Contains Dairy',
                'description' => 'Menu yang mengandung produk susu, perhatian bagi yang alergi susu.',
            ],
            [
                'name' => 'Contains Gluten',
                'description' => 'Menu yang mengandung gluten, perhatian bagi yang sensitif terhadap gluten.',
            ],
        ];

        foreach ($tags as $tag) {
            DietTag::create($tag);
        }
    }
}
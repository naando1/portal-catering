<?php

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
        $dietTags = [
            [
                'name' => 'Rendah Gula',
                'description' => 'Menu dengan kandungan gula ≤ 10g per porsi',
                'slug' => 'rendah-gula',
            ],
            [
                'name' => 'Rendah Sodium',
                'description' => 'Menu dengan kandungan sodium ≤ 600mg per porsi',
                'slug' => 'rendah-sodium',
            ],
            [
                'name' => 'Rendah Lemak',
                'description' => 'Menu dengan kandungan lemak ≤ 10g per porsi',
                'slug' => 'rendah-lemak',
            ],
            [
                'name' => 'Tinggi Serat',
                'description' => 'Menu dengan kandungan serat ≥ 3g per porsi',
                'slug' => 'tinggi-serat',
            ],
            [
                'name' => 'Karbohidrat Kompleks',
                'description' => 'Menu dengan karbohidrat kompleks yang lebih sehat',
                'slug' => 'karbo-kompleks',
            ],
            [
                'name' => 'Diabetes-Friendly',
                'description' => 'Menu yang aman untuk penderita diabetes',
                'slug' => 'diabetes-friendly',
            ],
            [
                'name' => 'Hipertensi-Friendly',
                'description' => 'Menu yang aman untuk penderita hipertensi',
                'slug' => 'hipertensi-friendly',
            ],
            [
                'name' => 'Jantung-Friendly',
                'description' => 'Menu yang aman untuk penderita penyakit jantung',
                'slug' => 'jantung-friendly',
            ],
            [
                'name' => 'Kolesterol-Friendly',
                'description' => 'Menu yang aman untuk penderita kolesterol tinggi',
                'slug' => 'kolesterol-friendly',
            ],
            [
                'name' => 'Ambeien-Friendly',
                'description' => 'Menu yang aman untuk penderita ambeien',
                'slug' => 'ambeien-friendly',
            ],
        ];

        foreach ($dietTags as $tag) {
            DietTag::updateOrCreate(
                ['name' => $tag['name']],
                [
                    'name' => $tag['name'],
                    'description' => $tag['description']
                ]
            );
        }
        
        $this->command->info('Diet tags seeded successfully!');
    }
}
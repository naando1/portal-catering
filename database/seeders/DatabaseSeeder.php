<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
            CategorySeeder::class,
            DietTagSeeder::class,
            CateringPartnerSeeder::class,
            DietMenuSeeder::class,
            SampleHealthProfileSeeder::class,
            SampleMenuSeeder::class,
        ]);
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\HealthProfile;
use App\Models\DietPreference;
use App\Models\UserBodyMetrics;
use App\Services\DietRecommendationService;
use Carbon\Carbon;

class SampleHealthProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan role customer ada
        $customerRole = Role::where('name', 'customer')->first();
        if (!$customerRole) {
            $this->command->error('Role customer tidak ditemukan. Jalankan RoleSeeder terlebih dahulu.');
            return;
        }

        // Buat beberapa user dengan profil kesehatan
        $users = [
            [
                'name' => 'User Diabetes',
                'email' => 'diabetes@example.com',
                'password' => bcrypt('password'),
                'role_id' => $customerRole->id,
                'birthdate' => Carbon::now()->subYears(45),
                'gender' => 'pria',
                'height' => 170,
                'weight' => 75,
                'activity_level' => 'sedang',
                'health' => [
                    'has_diabetes' => true,
                    'has_hypertension' => false,
                    'has_heart_disease' => false,
                    'has_cholesterol' => false,
                    'has_hemorrhoids' => false,
                ],
                'diet' => [
                    'diet_goal' => 'turun_bb',
                    'deficit_surplus_percentage' => 15,
                    'taste_preferences' => ['gurih', 'manis'],
                    'cooking_method_preferences' => ['kukus', 'rebus', 'panggang'],
                ]
            ],
            [
                'name' => 'User Hipertensi',
                'email' => 'hipertensi@example.com',
                'password' => bcrypt('password'),
                'role_id' => $customerRole->id,
                'birthdate' => Carbon::now()->subYears(55),
                'gender' => 'wanita',
                'height' => 160,
                'weight' => 70,
                'activity_level' => 'ringan',
                'health' => [
                    'has_diabetes' => false,
                    'has_hypertension' => true,
                    'has_heart_disease' => false,
                    'has_cholesterol' => true,
                    'has_hemorrhoids' => false,
                ],
                'diet' => [
                    'diet_goal' => 'jaga_bb',
                    'deficit_surplus_percentage' => 15,
                    'taste_preferences' => ['gurih', 'manis'],
                    'cooking_method_preferences' => ['kukus', 'rebus'],
                ]
            ],
            [
                'name' => 'User Jantung',
                'email' => 'jantung@example.com',
                'password' => bcrypt('password'),
                'role_id' => $customerRole->id,
                'birthdate' => Carbon::now()->subYears(60),
                'gender' => 'pria',
                'height' => 175,
                'weight' => 80,
                'activity_level' => 'sedentari',
                'health' => [
                    'has_diabetes' => false,
                    'has_hypertension' => true,
                    'has_heart_disease' => true,
                    'has_cholesterol' => true,
                    'has_hemorrhoids' => false,
                ],
                'diet' => [
                    'diet_goal' => 'turun_bb',
                    'deficit_surplus_percentage' => 15,
                    'taste_preferences' => ['gurih'],
                    'cooking_method_preferences' => ['kukus', 'rebus'],
                ]
            ],
            [
                'name' => 'User Kolesterol',
                'email' => 'kolesterol@example.com',
                'password' => bcrypt('password'),
                'role_id' => $customerRole->id,
                'birthdate' => Carbon::now()->subYears(50),
                'gender' => 'wanita',
                'height' => 165,
                'weight' => 68,
                'activity_level' => 'sedang',
                'health' => [
                    'has_diabetes' => false,
                    'has_hypertension' => false,
                    'has_heart_disease' => false,
                    'has_cholesterol' => true,
                    'has_hemorrhoids' => false,
                ],
                'diet' => [
                    'diet_goal' => 'turun_bb',
                    'deficit_surplus_percentage' => 15,
                    'taste_preferences' => ['manis', 'asin'],
                    'cooking_method_preferences' => ['kukus', 'rebus', 'panggang'],
                ]
            ],
            [
                'name' => 'User Ambeien',
                'email' => 'ambeien@example.com',
                'password' => bcrypt('password'),
                'role_id' => $customerRole->id,
                'birthdate' => Carbon::now()->subYears(35),
                'gender' => 'pria',
                'height' => 172,
                'weight' => 65,
                'activity_level' => 'sedang',
                'health' => [
                    'has_diabetes' => false,
                    'has_hypertension' => false,
                    'has_heart_disease' => false,
                    'has_cholesterol' => false,
                    'has_hemorrhoids' => true,
                ],
                'diet' => [
                    'diet_goal' => 'jaga_bb',
                    'deficit_surplus_percentage' => 15,
                    'taste_preferences' => ['gurih', 'manis'],
                    'cooking_method_preferences' => ['kukus', 'rebus'],
                ]
            ],
            [
                'name' => 'User Sehat',
                'email' => 'sehat@example.com',
                'password' => bcrypt('password'),
                'role_id' => $customerRole->id,
                'birthdate' => Carbon::now()->subYears(25),
                'gender' => 'wanita',
                'height' => 168,
                'weight' => 58,
                'activity_level' => 'berat',
                'health' => [
                    'has_diabetes' => false,
                    'has_hypertension' => false,
                    'has_heart_disease' => false,
                    'has_cholesterol' => false,
                    'has_hemorrhoids' => false,
                ],
                'diet' => [
                    'diet_goal' => 'naik_bb',
                    'deficit_surplus_percentage' => 15,
                    'taste_preferences' => ['pedas', 'gurih', 'manis'],
                    'cooking_method_preferences' => ['goreng', 'bakar', 'panggang'],
                ]
            ],
        ];

        $recommendationService = new DietRecommendationService();

        foreach ($users as $userData) {
            $healthData = $userData['health'];
            $dietData = $userData['diet'];
            
            unset($userData['health']);
            unset($userData['diet']);

            // Buat user
            $user = User::create($userData);

            // Buat health profile
            HealthProfile::create([
                'user_id' => $user->id,
                'has_diabetes' => $healthData['has_diabetes'],
                'has_hypertension' => $healthData['has_hypertension'],
                'has_heart_disease' => $healthData['has_heart_disease'],
                'has_cholesterol' => $healthData['has_cholesterol'],
                'has_hemorrhoids' => $healthData['has_hemorrhoids'],
            ]);

            // Buat diet preference
            DietPreference::create([
                'user_id' => $user->id,
                'diet_type' => 'regular',
                'diet_goal' => $dietData['diet_goal'],
                'deficit_surplus_percentage' => $dietData['deficit_surplus_percentage'],
                'taste_preferences' => json_encode($dietData['taste_preferences']),
                'cooking_method_preferences' => json_encode($dietData['cooking_method_preferences']),
            ]);

            // Hitung dan simpan metrics user
            $recommendationService->calculateAndStoreUserMetrics($user);
        }

        $this->command->info('Sample health profiles created successfully!');
    }
}
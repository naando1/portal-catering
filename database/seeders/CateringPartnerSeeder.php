<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\CateringPartner;
use Illuminate\Support\Facades\Hash;

class CateringPartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Dapatkan role partner
        $partnerRole = Role::where('name', 'partner')->first();
        
        if (!$partnerRole) {
            $this->command->error('Partner role not found! Make sure to run RoleSeeder first.');
            return;
        }
        
        $partners = [
            [
                'user' => [
                    'name' => 'Healthy Catering',
                    'email' => 'healthy@example.com',
                    'password' => Hash::make('password'),
                    'role_id' => $partnerRole->id,
                    'phone_number' => '081234567890',
                    'address' => 'Jl. Sehat No. 123, Jakarta'
                ],
                'business' => [
                    'business_name' => 'Healthy Catering',
                    'description' => 'Katering sehat dengan menu bergizi dan rendah kalori',
                    'logo' => 'partners/healthy.jpg',
                    'is_active' => true
                ]
            ],
            [
                'user' => [
                    'name' => 'Diet Delights',
                    'email' => 'diet@example.com',
                    'password' => Hash::make('password'),
                    'role_id' => $partnerRole->id,
                    'phone_number' => '081234567891',
                    'address' => 'Jl. Diet No. 456, Jakarta'
                ],
                'business' => [
                    'business_name' => 'Diet Delights',
                    'description' => 'Spesialis makanan diet dengan cita rasa lezat',
                    'logo' => 'partners/diet.jpg',
                    'is_active' => true
                ]
            ],
            [
                'user' => [
                    'name' => 'Nutrisi Nusantara',
                    'email' => 'nutrisi@example.com',
                    'password' => Hash::make('password'),
                    'role_id' => $partnerRole->id,
                    'phone_number' => '081234567892',
                    'address' => 'Jl. Nutrisi No. 789, Jakarta'
                ],
                'business' => [
                    'business_name' => 'Nutrisi Nusantara',
                    'description' => 'Makanan tradisional Indonesia dengan sentuhan sehat',
                    'logo' => 'partners/nutrisi.jpg',
                    'is_active' => true
                ]
            ],
        ];
        
        foreach ($partners as $partner) {
            // Buat atau update user
            $user = User::updateOrCreate(
                ['email' => $partner['user']['email']],
                $partner['user']
            );
            
            // Buat atau update catering partner
            CateringPartner::updateOrCreate(
                ['user_id' => $user->id],
                array_merge(['user_id' => $user->id], $partner['business'])
            );
        }
        
        $this->command->info('Catering partners seeded successfully!');
    }
}

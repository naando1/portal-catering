<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        
        if (!$adminRole) {
            $adminRole = Role::create(['name' => 'admin']);
        }
        
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@portal.com',
            'password' => Hash::make('admin123'),
            'role_id' => $adminRole->id
        ]);
    }
}

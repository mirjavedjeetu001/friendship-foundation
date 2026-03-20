<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin only
        $superAdmin = User::create([
            'name' => 'Allied Group Admin',
            'email' => 'alliedgroup@gmail.com',
            'password' => Hash::make('12345678'),
            'phone' => '01811480222',
            'is_active' => true,
            'status' => 'approved',
            'joined_date' => now(),
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole('super-admin');
    }
}
